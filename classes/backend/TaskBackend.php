<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Collab\Backend;


use HeimrichHannot\Collab\CollabConfig;
use HeimrichHannot\Collab\Helper\Helper;
use HeimrichHannot\Collab\Helper\TaskHelper;
use HeimrichHannot\Collab\Helper\TaskListHelper;
use HeimrichHannot\Collab\TaskListModel;
use HeimrichHannot\Collab\TaskModel;
use HeimrichHannot\Versions\VersionModel;

class TaskBackend extends \Backend
{

	public static function filterList()
	{
		$arrFilter = array();
		$objModels = TaskListModel::findExcludedByUserTypeAndGroups(Helper::getCurrentUserType(), Helper::getCurrentUserGroups());

		if ($objModels === null)
		{
			return $arrFilter;
		}

		while ($objModels->next())
		{
			$arrFilter[] = array('tasklist!=?', $objModels->id);
		}

		return $arrFilter;
	}

	public function setDefaults($strTable, $insertID, $arrSet, \DataContainer $dc)
	{
		if (($objModel = TaskModel::findByPk($insertID)) === null)
		{
			return;
		}

		$objModel->authorType   = Helper::getCurrentUserType();
		$objModel->assigneeType = Helper::getCurrentUserType();
		$objModel->author       = Helper::getCurrentUserId();
		$objModel->assignee     = Helper::getCurrentUserId();

		$objModel->save();
	}

	public function deleteAttachments(\DataContainer $dc, $insertID)
	{
		TaskHelper::deleteTaskAttachments($dc->id);
	}

	public function showHint(\DataContainer $dc)
	{
		$totalTasklists = TaskListModel::countAll();


		if ($totalTasklists > 0)
		{
			return;
		}


		$objTemplate        = new \BackendTemplate('hint_collab');
		$objTemplate->class = 'tasklist';
		$objTemplate->text  = $GLOBALS['TL_LANG']['COLLAB']['tasklist_hint'];

		\Message::addRaw($objTemplate->parse());
	}

	public function getTaskLists(\DataContainer $dc)
	{
		return TaskListHelper::getTaskListsAsOptionsForCurrentUser();
	}

	/**
	 * Get all author types as array
	 *
	 * @param \DataContainer $dc
	 *
	 * @return array
	 */
	public function getAuthorTypes(\DataContainer $dc)
	{
		return CollabConfig::getAuthorTypes();
	}

	/**
	 * Get all task types as array
	 *
	 * @param \DataContainer $dc
	 *
	 * @return array
	 */
	public function getTaskTypes(\DataContainer $dc)
	{
		return CollabConfig::getTaskTypes();
	}

	public function getAssigneesAsOptions(\DataContainer $dc)
	{
		return TaskHelper::getUserOptionsByTypeAndList($dc->activeRecord->assigneeType, $dc->activeRecord->tasklist);
	}

	public function getAuthorAsOptions(\DataContainer $dc)
	{
		return TaskHelper::getUserOptionsByTypeAndList($dc->activeRecord->authorType, $dc->activeRecord->tasklist);
	}

	public function toggleTask($intId, $blnVisible)
	{
		$objUser     = \BackendUser::getInstance();
		$objDatabase = \Database::getInstance();

		// Check permissions to publish
		if (!$objUser->isAdmin && !$objUser->hasAccess('tl_task::complete', 'alexf'))
		{
			\Controller::log('Not enough permissions to complete/open item ID "' . $intId . '"', 'TaskBackend toggleTask', TL_ERROR);
			\Controller::redirect('contao/main.php?act=error');
		}

		$objVersions = new \Versions('tl_task', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_task']['fields']['complete']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_task']['fields']['complete']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
			}
		}

		// Update the database
		$objDatabase->prepare("UPDATE tl_task SET tstamp=" . time() . ", complete='" . ($blnVisible ? 1 : '') . "' WHERE id=?")->execute($intId);

		$objVersions->create();
		\Controller::log('A new version of record "tl_task.id=' . $intId . '" has been created' . $this->getParentEntries('tl_task', $intId), 'TaskBackend toggleTask()', TL_GENERAL);
	}

	/**
	 * Add an image to each record
	 *
	 * @param array          $row
	 * @param string         $label
	 * @param \DataContainer $dc
	 * @param array          $args
	 *
	 * @return array
	 */
	public function listItem($row, $label, \DataContainer $dc, $args)
	{
		$blnChecked = \Widget::optionChecked($row['complete'], array('', 1));
		$checked    = $blnChecked ? ' checked="checked"' : '';
		$onclick    = ' onclick="CollabBackend.toggleTask(this,' . $row['id'] . ',\'' . $dc->table . '\');"';
		$title      = $GLOBALS['TL_LANG']['tl_task']['toggleTaskTitle'];

		$objTask = TaskModel::findByPk($row['id']);

		$args[0] = '<div class="complete_task"><input title="' . $title . '" type="checkbox" value="' . $row['complete'] . '" name="complete"' . $checked . $onclick . '/></div>';
		$args[3] = TaskHelper::getUserNameByTypeAndIdAndList($objTask->assigneeType, $objTask->assignee, $objTask->tasklist);
		$args[4] = TaskListHelper::getTaskListName($objTask->tasklist);

		return $args;
	}

	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$objUser = \BackendUser::getInstance();

		if (strlen(\Input::get('tid')))
		{
			$this->toggleVisibility(\Input::get('tid'), (\Input::get('state') == 1));
			\Controller::redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$objUser->isAdmin && !$objUser->hasAccess('tl_task::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> ';
	}

	public function toggleVisibility($intId, $blnVisible)
	{
		$objUser     = \BackendUser::getInstance();
		$objDatabase = \Database::getInstance();

		// Check permissions to publish
		if (!$objUser->isAdmin && !$objUser->hasAccess('tl_task::published', 'alexf'))
		{
			\Controller::log('Not enough permissions to publish/unpublish item ID "' . $intId . '"', 'TaskBackend toggleVisibility', TL_ERROR);
			\Controller::redirect('contao/main.php?act=error');
		}

		$objVersions = new \Versions('tl_task', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_task']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_task']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
			}
		}

		// Update the database
		$objDatabase->prepare("UPDATE tl_task SET tstamp=" . time() . ", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")->execute($intId);

		$objVersions->create();
		\Controller::log('A new version of record "tl_task.id=' . $intId . '" has been created' . $this->getParentEntries('tl_task', $intId), 'TaskBackend toggleVisibility()', TL_GENERAL);
	}

}
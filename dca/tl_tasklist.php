<?php

$GLOBALS['TL_DCA']['tl_tasklist'] = array
(
	'config'      => array
	(
		'dataContainer'     => 'Table',
		'switchToEdit'      => true,
		'enableVersioning'  => true,
		'onload_callback'   => array
		(
			array('tl_tasklist', 'checkPermission'),
		),
		'onsubmit_callback' => array
		(
			array('HeimrichHannot\Haste\Dca\General', 'setDateAdded'),
		),
		'sql'               => array
		(
			'keys' => array
			(
				'id' => 'primary',
			),
		),
	),
	'list'        => array
	(
		'label'             => array
		(
			'fields' => array('title'),
			'format' => '%s',
		),
		'sorting'           => array
		(
			'mode'        => 1,
			'fields'      => array('title'),
			'panelLayout' => 'filter;search,limit',
		),
		'global_operations' => array
		(
			'tasks' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_tasklist']['manageTasks'],
				'href'       => 'table=tl_task',
				'icon'       => 'system/modules/collab/assets/img/icon-collab.png',
				'attributes' => 'onclick="Backend.getScrollOffset();"',
			),
			'all'   => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();"',
			),
		),
		'operations'        => array
		(
			'edit'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_tasklist']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif',
			),
			'copy'   => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_tasklist']['copy'],
				'href'            => 'act=copy',
				'icon'            => 'copy.gif',
				'button_callback' => array('tl_tasklist', 'copyArchive'),
			),
			'delete' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_tasklist']['copy'],
				'href'            => 'act=delete',
				'icon'            => 'delete.gif',
				'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
									 . '\'))return false;Backend.getScrollOffset()"',
				'button_callback' => array('tl_tasklist', 'deleteArchive'),
			),
			'toggle' => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_tasklist']['toggle'],
				'href'  => 'act=toggle',
				'icon'  => 'toggle.gif',
			),
			'show'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_tasklist']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif',
			),
		),
	),
	'palettes'    => array
	(
		'__selector__' => array('protected', 'published'),
		'default'      => '{general_legend},title;{protected_legend:hide},protected;{publish_legend},published;',
	),
	'subpalettes' => array(
		'protected' => 'memberGroups,userGroups',
		'published' => 'start,stop',
	),
	'fields'      => array
	(
		'id'           => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment",
		),
		'tstamp'       => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_tasklist']['tstamp'],
			'sql'   => "int(10) unsigned NOT NULL default '0'",
		),
		'dateAdded'    => array
		(
			'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
			'sorting' => true,
			'flag'    => 6,
			'eval'    => array('rgxp' => 'datim', 'doNotCopy' => true),
			'sql'     => "int(10) unsigned NOT NULL default '0'",
		),
		'title'        => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_tasklist']['title'],
			'exclude'   => true,
			'search'    => true,
			'sorting'   => true,
			'flag'      => 1,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'protected'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_tasklist']['protected'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => array('submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'memberGroups' => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_tasklist']['memberGroups'],
			'exclude'    => true,
			'inputType'  => 'checkbox',
			'foreignKey' => 'tl_member_group.name',
			'eval'       => array('multiple' => true),
			'sql'        => "blob NULL",
			'relation'   => array('type' => 'hasMany', 'load' => 'lazy'),
		),
		'userGroups'   => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_tasklist']['userGroups'],
			'exclude'    => true,
			'inputType'  => 'checkbox',
			'foreignKey' => 'tl_user_group.name',
			'eval'       => array('mandatory' => true, 'multiple' => true),
			'sql'        => "blob NULL",
			'relation'   => array('type' => 'hasMany', 'load' => 'lazy'),
		),
		'published'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_tasklist']['published'],
			'exclude'   => true,
			'filter'    => true,
			'default'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('doNotCopy' => true, 'submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default '1'",
		),
		'start'        => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_tasklist']['start'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''",
		),
		'stop'         => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_tasklist']['stop'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''",
		),
	),
);

class tl_tasklist extends \Backend
{

	public function checkPermission()
	{
		$objUser     = \BackendUser::getInstance();
		$objSession  = \Session::getInstance();
		$objDatabase = \Database::getInstance();

		if ($objUser->isAdmin)
		{
			return;
		}

		// Set root IDs
		if (!is_array($objUser->tasklists) || empty($objUser->tasklists))
		{
			$root = array(0);
		} else
		{
			$root = $objUser->tasklists;
		}

		$GLOBALS['TL_DCA']['tl_tasklist']['list']['sorting']['root'] = $root;

		// Check permissions to add archives
		if (!$objUser->hasAccess('create', 'tasklistp'))
		{
			$GLOBALS['TL_DCA']['tl_tasklist']['config']['closed'] = true;
		}

		// Check current action
		switch (\Input::get('act'))
		{
			case 'create':
			case 'select':
				// Allow
				if (!$objUser->hasAccess('create', 'tasklistp'))
				{
					$this->log('Not enough permissions to ' . \Input::get('act') . ' tasklist ID "' . \Input::get('id') . '"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
			case 'edit':
				// Dynamically add the record to the user profile
				if (!in_array(\Input::get('id'), $root))
				{
					$arrNew = $objSession->get('new_records');

					if (is_array($arrNew['tl_tasklist']) && in_array(\Input::get('id'), $arrNew['tl_tasklist']))
					{
						// Add permissions on user level
						if ($objUser->inherit == 'custom' || !$objUser->groups[0])
						{
							$objUser = $objDatabase->prepare("SELECT tasklists, tasklistp FROM tl_user WHERE id=?")
								->limit(1)
								->execute($objUser->id);

							$arrModulep = deserialize($objUser->tasklistp);

							if (is_array($arrModulep) && in_array('create', $arrModulep))
							{
								$arrModules   = deserialize($objUser->tasklists);
								$arrModules[] = \Input::get('id');

								$objDatabase->prepare("UPDATE tl_user SET tasklists=? WHERE id=?")
									->execute(serialize($arrModules), $objUser->id);
							}
						} // Add permissions on group level
						elseif ($objUser->groups[0] > 0)
						{
							$objGroup = $objDatabase->prepare("SELECT tasklists, tasklistp FROM tl_user_group WHERE id=?")
								->limit(1)
								->execute($objUser->groups[0]);

							$arrModulep = deserialize($objGroup->tasklistp);

							if (is_array($arrModulep) && in_array('create', $arrModulep))
							{
								$arrModules   = deserialize($objGroup->tasklists);
								$arrModules[] = \Input::get('id');

								$objDatabase->prepare("UPDATE tl_user_group SET tasklists=? WHERE id=?")
									->execute(serialize($arrModules), $objUser->groups[0]);
							}
						}

						// Add new element to the user object
						$root[]     = \Input::get('id');
						$objUser->s = $root;
					}
				}
			// No break;

			case 'copy':
			case 'delete':
			case 'show':
				if (!in_array(\Input::get('id'), $root) || (\Input::get('act') == 'delete' && !$objUser->hasAccess('delete', 'tasklistp')))
				{
					$this->log('Not enough permissions to ' . \Input::get('act') . ' tasklist ID "' . \Input::get('id') . '"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
				$session = $objSession->getData();
				if (\Input::get('act') == 'deleteAll' && !$objUser->hasAccess('delete', 'tasklistp'))
				{
					$session['CURRENT']['IDS'] = array();
				} else
				{
					$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
				}
				$objSession->setData($session);
				break;

			default:
				if (strlen(\Input::get('act')))
				{
					$this->log('Not enough permissions to ' . \Input::get('act') . ' tasklist archives', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}

	public function editHeader($row, $href, $label, $title, $icon, $attributes)
	{
		return \BackendUser::getInstance()->canEditFieldsOf('tl_tasklist')
			? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes
			  . '>' . Image::getHtml($icon, $label) . '</a> '
			: \Image::getHtml(
				preg_replace('/\.gif$/i', '_.gif', $icon)
			) . ' ';
	}

	public function copyArchive($row, $href, $label, $title, $icon, $attributes)
	{
		return \BackendUser::getInstance()->hasAccess('create', 'tasklistp')
			? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>'
			  . Image::getHtml($icon, $label) . '</a> '
			: \Image::getHtml(
				preg_replace('/\.gif$/i', '_.gif', $icon)
			) . ' ';
	}

	public function deleteArchive($row, $href, $label, $title, $icon, $attributes)
	{
		return \BackendUser::getInstance()->hasAccess('delete', 'tasklistp')
			? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>'
			  . Image::getHtml($icon, $label) . '</a> '
			: \Image::getHtml(
				preg_replace('/\.gif$/i', '_.gif', $icon)
			) . ' ';
	}
}

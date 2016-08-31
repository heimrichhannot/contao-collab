<?php

namespace HeimrichHannot\Collab;

use HeimrichHannot\Collab\Helper\TaskHelper;
use HeimrichHannot\Haste\Util\Classes;
use HeimrichHannot\Versions\Version;
use HeimrichHannot\Versions\VersionModel;

class TaskModel extends \Contao\Model
{
	const TASK_FILTER_CRITERIA_NO_ASSIGNEE      = 'NO_ASSIGNEE';
	const TASK_FILTER_CRITERIA_HAS_ASSIGNEE     = 'HAS_ASSIGNEE';
	const TASK_FILTER_CRITERIA_NEW_TASK         = 'NEW_TASK';
	const TASK_FILTER_CRITERIA_PUBLISHED        = 'PUBLISHED';
	const TASK_FILTER_CRITERIA_ASSIGNEE_CHANGED = 'ASSIGNEE_CHANGED';
	const TASK_FILTER_CRITERIA_EXISTING_TASK    = 'EXISTING_TASK';
	const TASK_FILTER_CRITERIA_COMPLETE         = 'COMPLETE';
	const TASK_FILTER_CRITERIA_OPEN             = 'OPEN';

	protected static $strTable = 'tl_task';

	/**
	 * Initialize a new TaskModel with title, description and set attributes but do not save
	 *
	 * @param        $title         Task title
	 * @param string $description   Task description
	 * @param array  $arrAttributes Array of attributes that will be set as Taskmodel attributes
	 *
	 * @return TaskModel The non saved TaskModel
	 */
	public static function initTask($title, $description = '', array $arrAttributes = array())
	{
		$time = time();

		$objTask              = new static();
		$objTask->tstamp      = $time;
		$objTask->dateAdded   = $time;
		$objTask->type        = CollabConfig::TASK_TYPE_DEFAULT;
		$objTask->title       = $title;
		$objTask->description = $description;

		foreach ($arrAttributes as $strKey => $varValue)
		{
			$objTask->{$strKey} = $varValue;
		}

		return $objTask;
	}

	/**
	 * Create the Task, save in database and create new version
	 *
	 * @param TaskModel $objTask
	 * @param bool      $blnCreateVersion
	 *
	 * @return TaskModel|null Return the new created Model or null if something went wrong
	 */
	public static function createTask(TaskModel $objTask, $blnCreateVersion = true)
	{
		$objTask = $objTask->save();

		if ($blnCreateVersion && $objTask !== null)
		{
			Version::createFromModel($objTask);
		}

		return $objTask;
	}

	public static function findByListsAndCriteria(array $arrListIds, array $arrCriteria = array(), array $arrOptions = array())
	{
		list($arrColumns, $arrValues) = static::getConditionsByFilterCriteria($arrCriteria);

		$arrColumns[] = \Database::getInstance()->findInSet('tasklist', $arrListIds);

		$objModels = static::findBy($arrColumns, $arrValues, $arrOptions);

		if ($objModels === null)
		{
			return null;
		}

		return static::filterTasksByFilterCriteria($objModels);
	}

	public static function filterTasksByFilterCriteria(\Model\Collection $objModels, array $arrCriteria = array())
	{
		$arrRegistered = array();

		while ($objModels->next())
		{
			$objModel = $objModels->current();

			if (!static::doesTaskSatisfyFilterCriteria($objModel, $arrCriteria))
			{
				continue;
			}

			$arrRegistered[$objModel->{$objModel::getPk()}] = $objModel;
		}

		return static::createCollection(array_filter(array_values($arrRegistered)), $objModel->getTable());
	}

	public static function doesTaskSatisfyFilterCriteria(\Model $objModel, array $arrCriteria = array())
	{
		$blnSatisfy = true;

		foreach ($arrCriteria as $strCriteria)
		{
			if (!$blnSatisfy)
			{
				break;
			}

			switch ($strCriteria)
			{
				case static::TASK_FILTER_CRITERIA_ASSIGNEE_CHANGED:
					$objPreviousAssignee = TaskHelper::getPreviousAssignee($objModel);

					// no previous version assignee, use TASK_FILTER_CRITERIA_HAS_ASSIGNEE as criteria
					if ($objPreviousAssignee === null)
					{
						$blnSatisfy = false;
						continue;
					}

					$blnSatisfy = ($objModel->assignee != $objPreviousAssignee->id);
					break;
			}
		}

		return $blnSatisfy;
	}

	public static function getConditionsByFilterCriteria(array $arrCriteria = array())
	{
		$t          = static::$strTable;
		$arrColumns = array();
		$arrValues  = array();

		foreach ($arrCriteria as $strCriteria)
		{
			switch ($strCriteria)
			{
				case static::TASK_FILTER_CRITERIA_COMPLETE:
					if (isset($arrCriteria[static::TASK_FILTER_CRITERIA_OPEN]))
					{
						break;
					}
					$arrColumns[] = "$t.complete = 1";
					break;
				case static::TASK_FILTER_CRITERIA_OPEN:
					if (isset($arrCriteria[static::TASK_FILTER_CRITERIA_COMPLETE]))
					{
						break;
					}
					$arrColumns[] = "$t.complete != 1";
					break;
				case static::TASK_FILTER_CRITERIA_NEW_TASK:
					if (isset($arrCriteria[static::TASK_FILTER_CRITERIA_EXISTING_TASK]))
					{
						break;
					}
					$arrColumns[] = "$t.tstamp > 0 AND $t.tstamp = $t.dateAdded";
					break;
				case static::TASK_FILTER_CRITERIA_EXISTING_TASK:
					if (isset($arrCriteria[static::TASK_FILTER_CRITERIA_NEW_TASK]))
					{
						break;
					}
					$arrColumns[] = "$t.tstamp > 0 AND $t.tstamp > $t.dateAdded";
					break;
				case static::TASK_FILTER_CRITERIA_HAS_ASSIGNEE:
					if (isset($arrCriteria[static::TASK_FILTER_CRITERIA_NO_ASSIGNEE]))
					{
						break;
					}
					$arrColumns[] = "$t.assignee > 0";
					break;
				case static::TASK_FILTER_CRITERIA_NO_ASSIGNEE:
					if (isset($arrCriteria[static::TASK_FILTER_CRITERIA_HAS_ASSIGNEE]))
					{
						break;
					}
					$arrColumns[] = "$t.assignee = 0";
					break;
				case static::TASK_FILTER_CRITERIA_PUBLISHED:

					if (!BE_USER_LOGGED_IN)
					{
						$time         = \Date::floorToMinute();
						$arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
					}

					break;
			}
		}

		return array($arrColumns, $arrValues);
	}

	public static function getTaskFilterCriteria()
	{
		return Classes::getConstantsByPrefixes(__CLASS__, array('TASK_FILTER_CRITERIA'));
	}
}
<?php

namespace HeimrichHannot\Collab;

class TaskModel extends \Model
{
	const TASK_FILTER_CRITERIA_NO_ASSIGNEE = 'NO_ASSIGNEE';
	const TASK_FILTER_CRITERIA_NEW_TASK    = 'NEW_TASK';
	const TASK_FILTER_CRITERIA_PUBLISHED   = 'PUBLISHED';

	protected static $strTable = 'tl_task';


	public static function findByListsAndCriteria(array $arrListIds, array $arrCriteria = array(), array $arrOptions = array())
	{
		list($arrColumns, $arrValues) = static::getConditionsFromFilterCriteria($arrCriteria);

		$arrColumns[] = \Database::getInstance()->findInSet('tasklist', $arrListIds);

		return static::findBy($arrColumns, $arrValues, $arrOptions);
	}

	public static function getConditionsFromFilterCriteria(array $arrCriteria = array())
	{
		$t          = static::$strTable;
		$arrColumns = array();
		$arrValues  = array();

		foreach ($arrCriteria as $strCriteria)
		{
			switch ($strCriteria)
			{
				case static::TASK_FILTER_CRITERIA_NEW_TASK:
					$arrColumns[] = "$t.tstamp > 0 AND $t.tstamp = $t.dateAdded";
					break;
				case static::TASK_FILTER_CRITERIA_NO_ASSIGNEE:
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
		return array(
			TaskModel::TASK_FILTER_CRITERIA_NO_ASSIGNEE,
			TaskModel::TASK_FILTER_CRITERIA_NEW_TASK,
			TaskModel::TASK_FILTER_CRITERIA_PUBLISHED,
		);
	}
}
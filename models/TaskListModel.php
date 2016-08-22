<?php

namespace HeimrichHannot\Collab;

class TaskListModel extends \Model
{

	protected static $strTable = 'tl_tasklist';


	/**
	 * Find published task lists by user group type and groups
	 *
	 * @param array $strType    The user group type
	 * @param array $arrGroups  An array of user groups
	 * @param array $arrOptions An optional options array
	 *
	 * @return \NewsModel|null The model or null if there are no observers
	 */
	public static function findPublishedByUserTypeAndGroups($strType, array $arrGroups = array(), array $arrOptions = array())
	{
		$t          = static::$strTable;
		$arrColumns = array();

		if (!BE_USER_LOGGED_IN)
		{
			$time         = \Date::floorToMinute();
			$arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
		}

		if(!$arrOptions['order'])
		{
			$arrOptions['order'] = "$t.title";
		}

		$objModels = static::findBy($arrColumns, null, $arrOptions);

		if($objModels === null || \BackendUser::getInstance()->isAdmin)
		{
			return $objModels;
		}

		$arrIds = array();

		while ($objModels->next())
		{
			$arrMatch = array();

			if(!$objModels->protected)
			{
				$arrIds[] = $objModels->id;
				continue;
			}

			switch ($strType)
			{
				case CollabConfig::AUTHOR_TYPE_USER:
					$arrMatch = array_intersect(deserialize($objModels->userGroups, true), $arrGroups);
				break;
				case CollabConfig::AUTHOR_TYPE_MEMBER:
					$arrMatch = array_intersect(deserialize($objModels->memberGroups, true), $arrGroups);
				break;
			}

			if(empty($arrMatch))
			{
				continue;
			}

			$arrIds[] = $objModels->id;
		}

		return static::findMultipleByIds($arrIds, $arrOptions);
	}

	/**
	 * Find excluded task lists by user group type and groups
	 *
	 * @param array $strType    The user group type
	 * @param array $arrGroups  An array of user groups
	 * @param array $arrOptions An optional options array
	 *
	 * @return \NewsModel|null The model or null if there are no observers
	 */
	public static function findExcludedByUserTypeAndGroups($strType, array $arrGroups = array(), array $arrOptions = array())
	{
		$t          = static::$strTable;

		if(!$arrOptions['order'])
		{
			$arrOptions['order'] = "$t.title";
		}

		$objModels = static::findAll($arrOptions);

		if($objModels === null || \BackendUser::getInstance()->isAdmin)
		{
			return null;
		}

		$arrExclude = array();

		while ($objModels->next())
		{
			$arrMatch = array();

			if(!$objModels->protected)
			{
				continue;
			}

			switch ($strType)
			{
				case CollabConfig::AUTHOR_TYPE_USER:
					$arrMatch = array_intersect(deserialize($objModels->userGroups, true), $arrGroups);
					break;
				case CollabConfig::AUTHOR_TYPE_MEMBER:
					$arrMatch = array_intersect(deserialize($objModels->memberGroups, true), $arrGroups);
					break;
			}

			if(empty($arrMatch))
			{
				$arrExclude[] = $objModels->id;
				continue;
			}
		}

		return static::findMultipleByIds($arrExclude, $arrOptions);
	}

}
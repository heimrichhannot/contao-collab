<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Collab\Helper;


use HeimrichHannot\Collab\CollabConfig;
use HeimrichHannot\Collab\TaskListModel;

class TaskListHelper extends Helper
{
	public static function getTaskListsAsOptionsForCurrentUser()
	{
		$objModels = TaskListModel::findPublishedByUserTypeAndGroups(Helper::getCurrentUserType(), Helper::getCurrentUserGroups());

		if($objModels === null)
		{
			return array();
		}

		return $objModels->fetchEach('title');
	}

	public static function getTaskListName($indId)
	{
		$objTaskList = TaskListModel::findByPk($indId);

		if($objTaskList === null)
		{
			return '';
		}

		return $objTaskList->title;
	}
}
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
use HeimrichHannot\Haste\Dca\Member;
use HeimrichHannot\Haste\Dca\User;
use HeimrichHannot\Haste\Model\MemberModel;
use HeimrichHannot\Haste\Model\UserModel;
use HeimrichHannot\Haste\Util\Arrays;
use HeimrichHannot\Versions\VersionModel;
use Leafo\ScssPhp\Version;


class TaskHelper extends Helper
{
	public static function getUserByIdAndType($intId, $strType, $blnActive = true, $blnMemberOnly = true)
	{
		switch ($strType)
		{
			case CollabConfig::AUTHOR_TYPE_MEMBER:
				return $blnActive ? MemberModel::findActiveById($intId) : MemberModel::findByPk($intId);
			case CollabConfig::AUTHOR_TYPE_USER:
				if ($blnMemberOnly)
				{
					return $blnActive ? UserModel::findActiveById($intId) : UserModel::findByPk($intId);
				}
		}

		return null;
	}

	public static function getCurrentAuthor(\Model $objTask, $blnActive = true, $blnMemberOnly = true)
	{
		return static::getUserByIdAndType($objTask->author, $objTask->authorType, $blnActive, $blnMemberOnly);
	}

	public static function getCurrentAssignee(\Model $objTask, $blnActive = true, $blnMemberOnly = true)
	{
		return static::getUserByIdAndType($objTask->assignee, $objTask->assigneeType, $blnActive, $blnMemberOnly);
	}

	public static function getPreviousAssignee(\Model $objTask, $blnMemberOnly = true, $blnActive = true)
	{
		$objPreviousVersions = VersionModel::findPreviousByModelAndDataValues($objTask, array('assignee' => '([1-9]+)'));

		if ($objPreviousVersions === null)
		{
			return null;
		}

		$arrData = deserialize($objPreviousVersions->data);

		// assigneeType has changed, we cant compare users with members
		if ($arrData['assigneeType'] != $objTask->assigneeType)
		{
			return null;
		}

		if ($arrData['assignee'] != 0)
		{
			return static::getUserByIdAndType($arrData['assignee'], $arrData['assigneeType'], $blnActive, $blnMemberOnly);
		}


		return null;
	}

	public static function getUserOptionsByTypeAndList($strType, $intList = 0)
	{
		$arrOptions = array();
		$objTaskList = TaskListModel::findPublishedById($intList);
		$arrGroups = array();

		switch ($strType)
		{
			case CollabConfig::AUTHOR_TYPE_MEMBER:
			default:

				if($objTaskList !== null && $objTaskList->protected)
				{
					$arrGroups = deserialize($objTaskList->memberGroups, true);
				}

				if(FE_USER_LOGGED_IN)
				{
					$arrGroups = array_intersect(deserialize(\FrontendUser::getInstance()->groups, true), $arrGroups);
				}

				if(empty($arrGroups) && !FE_USER_LOGGED_IN)
				{
					return Member::getMembersAsOptions();
				}

				$objMembers = MemberModel::findActiveByGroups($arrGroups);

				if($objMembers === null)
				{
					return $arrOptions;
				}

				return Arrays::concatArrays(' ', $objMembers->fetchEach('firstname'), $objMembers->fetchEach('lastname'));

			case CollabConfig::AUTHOR_TYPE_USER:

				if($objTaskList !== null && $objTaskList->protected)
				{
					$arrGroups = deserialize($objTaskList->userGroups, true);
				}

				if(BE_USER_LOGGED_IN)
				{
					$arrGroups = array_intersect(deserialize(\BackendUser::getInstance()->groups, true), $arrGroups);
				}

				if(empty($arrGroups) && !BE_USER_LOGGED_IN)
				{
					return User::getUsersAsOptions();
				}

				$objUsers = UserModel::findActiveByGroups($arrGroups);

				if($objUsers === null)
				{
					return $arrOptions;
				}

				return $objUsers->fetchEach('name');
		}

		return $arrOptions;
	}

	public static function getUserNameByTypeAndIdAndList($strType, $intId)
	{
		$arrOptions = static::getUserOptionsByTypeAndList($strType);

		return isset($arrOptions[$intId]) ? $arrOptions[$intId] : '';
	}

	public static function deleteTaskAttachments($intTaskId)
	{
		$strFolder = ltrim(TaskHelper::getTaskAttachmentSRC($intTaskId, true), '/');

		if (!file_exists(TL_ROOT . '/' . $strFolder) || !is_dir(TL_ROOT . '/' . $strFolder))
		{
			return false;
		}

		$objFolder = new \Folder($strFolder);
		$objFolder->delete();

		return true;
	}

	public static function getTaskAttachmentSRC($intTaskId, $blnReturnPath = false)
	{
		return CollabConfig::getTaskAttachmentSRC($blnReturnPath) . '/' . $intTaskId . '/';
	}
}
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
		$objPreviousVersions = VersionModel::findAllPreviousByModel($objTask);

		if ($objPreviousVersions === null)
		{
			return null;
		}

		while ($objPreviousVersions->next())
		{
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
		}


		return null;
	}

	public static function getUserOptionsByType($strType)
	{
		switch ($strType)
		{
			case CollabConfig::AUTHOR_TYPE_MEMBER:
				return Member::getMembersAsOptions();
			case CollabConfig::AUTHOR_TYPE_USER:
				return User::getUsersAsOptions();
		}

		return array();
	}

	public static function getUserNameByTypeAndId($strType, $intId)
	{
		$arrOptions = static::getUserOptionsByType($strType);

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
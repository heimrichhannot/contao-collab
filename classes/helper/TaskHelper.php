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
use HeimrichHannot\Haste\Dca\Member;
use HeimrichHannot\Haste\Dca\User;

class TaskHelper extends Helper
{
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

		if(!file_exists(TL_ROOT . '/' . $strFolder) || !is_dir(TL_ROOT . '/' . $strFolder))
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
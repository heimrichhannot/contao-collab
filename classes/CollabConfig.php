<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Collab;


use HeimrichHannot\Haste\Util\Classes;

class CollabConfig
{
	const OBSERVER_TASKS_FROM_MAILS        = 'tasks_from_mails';
	const OBSERVER_NOTIFICATION_FROM_TASKS = 'notification_from_task';
	const OBSERVER_SUBJECT_TASK            = 'collab_task';

	const AUTHOR_TYPE_NONE   = 'none';
	const AUTHOR_TYPE_MEMBER = 'member';
	const AUTHOR_TYPE_USER   = 'user';

	const TASK_TYPE_DEFAULT = 'default';
	const TASK_TYPE_MAIL    = 'mail';

	public static function getAuthorTypes()
	{
		return Classes::getConstantsByPrefixes(__CLASS__, array('AUTHOR_TYPE_'));
	}

	public static function getTaskTypes()
	{
		return Classes::getConstantsByPrefixes(__CLASS__, array('TASK_TYPE_'));
	}

	public static function getAttachmentSRC($blnReturnPath = false, $directory = null)
	{
		if ($directory === null)
		{
			$directory = \Config::get('collab_default_upload_directory');
		}

		$objFolder = new \Folder($directory);

		if ($blnReturnPath)
		{
			return $objFolder->path;
		}

		if (\Validator::isUuid($objFolder->getModel()->uuid))
		{
			return \StringUtil::binToUuid($objFolder->getModel()->uuid);
		}

		return null;
	}

	public static function getDefaultAttachmentSRC($blnReturnPath = false)
	{
		return static::getAttachmentSRC($blnReturnPath);
	}

	public static function getTaskAttachmentSRC($blnReturnPath = false)
	{
		return static::getAttachmentSRC($blnReturnPath, \Config::get('collab_task_attachment_directory'));
	}
}
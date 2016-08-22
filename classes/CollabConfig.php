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


class CollabConfig
{
	const OBSERVER_TASKS_FROM_MAILS = 'tasks_from_mails';
	const OBSERVER_SUBJECT_TASK     = 'collab_task';

	const COLLAB_DEFAULT_UPLOAD_DIRECTORY  = 'files/collab/uploads';
	const COLLAB_TASK_ATTACHMENT_DIRECTORY = 'files/collab/tasks';

	const AUTHOR_TYPE_NONE   = 'none';
	const AUTHOR_TYPE_MEMBER = 'member';
	const AUTHOR_TYPE_USER   = 'user';

	const TASK_TYPE_DEFAULT = 'default';
	const TASK_TYPE_MAIL    = 'mail';

	public static function getTaskTypes()
	{
		return array(static::TASK_TYPE_DEFAULT, static::TASK_TYPE_MAIL);
	}

	public static function getAttachmentSRC($blnReturnPath = false, $directory = null)
	{
		if ($directory === null)
		{
			$directory = static::COLLAB_DEFAULT_UPLOAD_DIRECTORY;
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
		return static::getAttachmentSRC($blnReturnPath, static::COLLAB_TASK_ATTACHMENT_DIRECTORY);
	}
}
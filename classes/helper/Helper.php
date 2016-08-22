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

class Helper
{

	public static function getCurrentUserGroups()
	{
		return TL_MODE == 'FE' ? deserialize(\FrontendUser::getInstance()->groups, true) : deserialize(\BackendUser::getInstance()->groups, true);
	}

	public static function getCurrentUserId()
	{
		return TL_MODE == 'FE' ? \FrontendUser::getInstance()->id : \BackendUser::getInstance()->id;
	}

	public static function getCurrentUserType()
	{
		return TL_MODE == 'FE' ? CollabConfig::AUTHOR_TYPE_MEMBER : CollabConfig::AUTHOR_TYPE_USER;
	}
}
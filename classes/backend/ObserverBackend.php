<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Collab\Backend;


use HeimrichHannot\Collab\Helper\TaskListHelper;
use HeimrichHannot\Collab\TaskModel;

class ObserverBackend extends \Backend
{

	public function getTaskCriteria(\DataContainer $dc)
	{
		return TaskModel::getTaskFilterCriteria();
	}

	public function getTaskLists(\DataContainer $dc)
	{
		return TaskListHelper::getTaskListsAsOptionsForCurrentUser();
	}
}
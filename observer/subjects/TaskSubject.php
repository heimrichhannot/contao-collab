<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Collab\Observer;


use HeimrichHannot\Collab\TaskModel;
use HeimrichHannot\Observer\ObserverLog;
use HeimrichHannot\Observer\Subject;

class TaskSubject extends Subject
{
	public function run()
	{
		$arrLists = deserialize($this->getModel()->tasklists, true);
		$arrCriteria = deserialize($this->getModel()->taskCriteria, true);

		$objTasks = TaskModel::findByListsAndCriteria($arrLists, $arrCriteria);

		if($objTasks === null)
		{
			if($this->objModel->debug)
			{
				ObserverLog::add($this->objModel->id, 'No tasks for given filter found.', __CLASS__ . ':' . __METHOD__);
			}

			return true;
		}

		if($this->objModel->debug)
		{
			$count = $objTasks->count();
			ObserverLog::add($this->objModel->id, $count . ($count == 1 ? ' Task' : ' Tasks') .' found for given filter.', __CLASS__ . ':' . __METHOD__);
		}

		$this->setRunIds($objTasks->fetchEach('id'));

		return true;
	}
}
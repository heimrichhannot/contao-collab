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
	/**
	 * Run the task observer
	 *
	 * @return bool True on success, false on error
	 */
	public function notify()
	{
		$arrLists    = deserialize($this->getModel()->tasklists, true);
		$arrCriteria = deserialize($this->getModel()->taskCriteria, true);

		$objTasks = TaskModel::findByListsAndCriteria($arrLists, $arrCriteria);

		if ($objTasks === null)
		{
			if ($this->objModel->debug)
			{
				ObserverLog::add($this->objModel->id, 'No tasks for given filter found.', __CLASS__ . ':' . __METHOD__);
			}

			return;
		}

		if ($this->objModel->debug)
		{
			$count = $objTasks->count();
			ObserverLog::add($this->objModel->id, $count . ($count == 1 ? ' Task' : ' Tasks') . ' found for given filter.', __CLASS__ . ':' . __METHOD__);
		}

		while ($objTasks->next())
		{
			$this->context = $objTasks->current();

			if (!$this->waitForContext($this->context))
			{
				if ($this->getModel()->debug)
				{
					ObserverLog::add($this->getModel()->id, 'Observers updated with task: "' . $objTasks->title . '" [ID:' . $objTasks->id .  '].', __CLASS__ . ':' . __METHOD__);
				}

				foreach ($this->observers as $obs)
				{
					$obs->update($this);
				}

				continue;
			}

			if ($this->getModel()->debug)
			{
				ObserverLog::add($this->getModel()->id, 'Waiting time for task: "' . $objTasks->title . '" [ID:' . $objTasks->id .  '] not elapsed yet.', __CLASS__ . ':' . __METHOD__);
			}
		}

		return true;
	}
}
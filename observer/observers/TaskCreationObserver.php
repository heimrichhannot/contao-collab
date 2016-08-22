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

use HeimrichHannot\Observer\Observer;
use HeimrichHannot\Observer\ObserverConfig;

abstract class TaskCreationObserver extends Observer
{
	protected $objTask;

	protected function doUpdate()
	{
		$blnReturn = $this->createTask();

		if(!$this->getState())
		{
			$this->setState($blnReturn ? Observer::STATE_SUCCESS : Observer::STATE_ERROR);
		}
	}

	abstract protected function createTask();

	public static function getPalettes(\DataContainer $dc = null)
	{
		return array
		(
			ObserverConfig::OBSERVER_SUBJECT_MAIl => 'tasklist',
		);
	}

}
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


use Contao\Versions;
use HeimrichHannot\Collab\CollabConfig;
use HeimrichHannot\Collab\Helper\TaskHelper;
use HeimrichHannot\Observer\Observer;
use HeimrichHannot\Versions\Version;
use HeimrichHannot\Versions\VersionModel;

class TaskRemoveAssigneeObserver extends Observer
{
	protected function doUpdate()
	{
		$this->getSubject()->getContext()->tstamp = time();
		$this->getSubject()->getContext()->assignee = 0;
		$this->getSubject()->getContext()->save();

		Version::createFromModel($this->getSubject()->getContext());

		if(!$this->getState())
		{
			$this->setState(Observer::STATE_SUCCESS);
		}
	}

	public static function getPalettes(\DataContainer $dc = null)
	{
		return array(
			CollabConfig::OBSERVER_SUBJECT_TASK => '',
		);
	}

	protected function getEntityId()
	{
		// Add the version to the entity id, as we build on versions
		$objVersion = VersionModel::findCurrentByModel($this->getSubject()->getContext());

		return sprintf(
			'%s_%s:%s_%s',
			$this->getSubject()->getContext()->getTable(),
			$this->getSubject()->getContext()->id,
			'tl_version',
			$objVersion === null ? '0' : $objVersion->id
		);
	}


}
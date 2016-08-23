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


use HeimrichHannot\Collab\CollabConfig;
use HeimrichHannot\Observer\NotificationObserver;
use HeimrichHannot\Observer\Observer;
use HeimrichHannot\Observer\ObserverNotification;

class TaskNotificationObserver extends NotificationObserver
{
	protected function createNotification()
	{
		ObserverNotification::sendNotification(
			$this->objSubject->getModel()->notification,
			$this->objSubject->getModel(),
			$this->objSubject->getContext(),
			$this->getMember()
		);

		// as mails might sent from queue it is not possible to determine if there were mail errors
		return Observer::STATE_SUCCESS;
	}

	protected function getEntityId()
	{
		return sprintf(
			'%s_%s:%s_%s',
			$this->getSubject()->getContext()->getTable(),
			$this->getSubject()->getContext()->id,
			$this->getMember()->getTable(),
			$this->getMember()->id
		);

	}

	public static function getPalettes(\DataContainer $dc = null)
	{
		return array(
			CollabConfig::OBSERVER_SUBJECT_TASK => 'notification,members,memberGroups,limitMembers',
		);
	}


}
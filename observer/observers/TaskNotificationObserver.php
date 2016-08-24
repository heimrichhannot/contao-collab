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
use HeimrichHannot\Collab\Helper\TaskHelper;
use HeimrichHannot\Haste\Model\Model;
use HeimrichHannot\Observer\NotificationObserver;
use HeimrichHannot\Observer\Observer;
use HeimrichHannot\Observer\ObserverNotification;
use HeimrichHannot\Versions\VersionModel;

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
		// Add the version to the entity id, as we build on versions
		$objVersion = VersionModel::findCurrentByModel($this->getSubject()->getContext());

		return sprintf(
			'%s_%s:%s_%s:%s_%s',
			$this->getSubject()->getContext()->getTable(),
			$this->getSubject()->getContext()->id,
			$this->getMember()->getTable(),
			$this->getMember()->id,
			'tl_version',
			$objVersion === null ? '0' : $objVersion->id
		);

	}

	/**
	 * Modify members
	 *
	 * @param \Model\Collection|null $objMembers
	 *
	 * @return \Model\Collection|null Return the collection of member entities or null
	 */
	protected function modifyMembers($objMembers = null)
	{
		// notify if assigneeType is member and authorType is not user and author != assignee
		if ($this->getSubject()->getContext()->assigneeType == CollabConfig::AUTHOR_TYPE_MEMBER)
		{
			$objAuthor = TaskHelper::getCurrentAuthor($this->getSubject()->getContext());
			$objAssignee = TaskHelper::getCurrentAssignee($this->getSubject()->getContext());

			// add current assignee
			if ($this->getSubject()->getModel()->notifyAssignee && $objAssignee !== null)
			{
				// skip if author is assignee
				if ($objAuthor->id != $objAssignee->id)
				{
					$objMembers = Model::addModelToCollection($objAssignee, $objMembers);
				}
			}

			// add previous assignee
			if($this->getSubject()->getModel()->notifyPreviousAssignee)
			{
				if (($objPreviousAssignee = TaskHelper::getPreviousAssignee($this->getSubject()->getContext())) !== null)
				{
					$objMembers = Model::addModelToCollection($objPreviousAssignee, $objMembers);
				}
			}

			// remove current assignee
			if($this->getSubject()->getModel()->skipNotifyAssignee && $objAssignee !== null)
			{
				$objMembers = Model::removeModelFromCollection($objAssignee, $objMembers);
			}

			// remove previous assignee
			if($this->getSubject()->getModel()->skipNotifyPreviousAssignee)
			{
				if (($objPreviousAssignee = TaskHelper::getPreviousAssignee($this->getSubject()->getContext())) !== null)
				{
					$objMembers = Model::removeModelFromCollection($objPreviousAssignee, $objMembers);
				}
			}
		}

		return $objMembers;
	}

	public static function getPalettes(\DataContainer $dc = null)
	{
		return array(
			CollabConfig::OBSERVER_SUBJECT_TASK => 'notification,notifyAssignee,notifyPreviousAssignee,members,memberGroups,limitMembers,skipNotifyAssignee,skipNotifyPreviousAssignee,',
		);
	}


}
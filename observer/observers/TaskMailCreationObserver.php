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
use HeimrichHannot\Collab\TaskModel;
use HeimrichHannot\Observer\Observer;
use HeimrichHannot\Observer\ObserverConfig;
use HeimrichHannot\Observer\ObserverLog;

class TaskMailCreationObserver extends TaskCreationObserver
{
	protected function createTask()
	{
		/**  @var \PhpImap\IncomingMail $objMail */
		$objMail = $this->getSubject()->getContext();


		$arrTaskAttributes = array(
			'type'      => CollabConfig::TASK_TYPE_MAIL,
			'fromName'  => $objMail->fromName,
			'fromEmail' => $objMail->fromAddress,
			'tasklist'  => $this->getSubject()->getModel()->tasklist,
		);

		$this->objTask = TaskModel::createTask(TaskModel::initTask($objMail->subject, $objMail->textPlain, $arrTaskAttributes));

		if ($this->objTask === null)
		{
			$this->setState(Observer::STATE_ERROR);

			return false;
		}

		$arrAttachments = $objMail->getAttachments();

		if (!empty($arrAttachments))
		{
			$this->saveAttachments($arrAttachments);
		}

		ObserverLog::add(
			$this->getSubject()->getModel()->id,
			'Created new task from mail: "' . $objMail->subject . ' ' . $objMail->messageId . '" sent from: ' . $objMail->fromAddress,
			__CLASS__ . ':' . __METHOD__ . '()'
		);

		return true;
	}

	/**
	 * Move attachments to the task attachment folder, files has been saved by MailSubject already to ObserverConfig::OBSERVER_DIRECTORY_ATTACHMENT
	 *
	 * @param array $arrAttachments
	 */
	protected function saveAttachments(array $arrAttachments)
	{
		$arrUuids = array();

		/** @var \PhpImap\IncomingMailAttachment $objAttachment */
		foreach ($arrAttachments as $objAttachment)
		{
			if (!file_exists($objAttachment->filePath))
			{
				continue;
			}

			$strPath = ltrim(str_replace(TL_ROOT, '', $objAttachment->filePath), '/');

			$objFile = new \File($strPath);

			$objFile->renameTo(TaskHelper::getTaskAttachmentSRC($this->objTask->id, true) . $objFile->name);
			$arrUuids[] = $objFile->getModel()->uuid;
		}

		$this->objTask->attachments = $arrUuids;
		$this->objTask->save();
	}

	public static function getPalettes(\DataContainer $dc = null)
	{
		return array(
			ObserverConfig::OBSERVER_SUBJECT_MAIl => 'tasklist',
		);
	}

	protected function getEntityId()
	{
		return $this->objSubject->getContext()->messageId;
	}

}
<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$arrDca = &$GLOBALS['TL_DCA']['tl_observer'];

/**
 * Palettes
 */
$arrDca['palettes'][\HeimrichHannot\Collab\CollabConfig::OBSERVER_SUBJECT_TASK] = '{general_legend},subject,title;{task_legend},tasklists,taskCriteria;{cronjob_legend},cronInterval,useCronExpression,priority,invoked;{observer_legend},observer;{expert_legend},debug,addObserverCriteria;{publish_legend},published;';


/**
 * Fields
 */
$arrFields = array
(
	'tasklist'  => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_observer']['tasklist'],
		'exclude'          => true,
		'inputType'        => 'select',
		'options_callback' => array('HeimrichHannot\Collab\Backend\ObserverBackend', 'getTaskLists'),
		'sql'              => "int(10) unsigned NOT NULL default '0'",
		'eval'             => array('tl_class' => 'w50 clr', 'includeBlankOption' => true),
	),
	'taskCriteria' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_observer']['taskCriteria'],
		'exclude'          => true,
		'inputType'        => 'checkboxWizard',
		'options_callback' => array('HeimrichHannot\Collab\Backend\ObserverBackend', 'getTaskCriteria'),
		'eval'             => array('tl_class' => 'clr wizard', 'multiple' => true),
		'sql'              => "blob NULL",
	),
	'tasklists' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_observer']['tasklists'],
		'exclude'          => true,
		'inputType'        => 'checkboxWizard',
		'options_callback' => array('HeimrichHannot\Collab\Backend\ObserverBackend', 'getTaskLists'),
		'eval'             => array('tl_class' => 'w50'),
		'sql'              => "blob NULL",
	),
);

$arrDca['fields'] = array_merge($arrDca['fields'], $arrFields);
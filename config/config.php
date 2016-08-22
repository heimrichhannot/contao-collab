<?php

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['content']['collab'] = array(
	'tables' => array('tl_task', 'tl_tasklist'),
	'icon'   => 'system/modules/collab/assets/img/icon-collab.png',
);


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_task']     = 'HeimrichHannot\Collab\TaskModel';
$GLOBALS['TL_MODELS']['tl_tasklist'] = 'HeimrichHannot\Collab\TaskListModel';

/**
 * Observer actions
 */
array_insert(
	$GLOBALS['OBSERVER']['OBSERVERS'],
	0,
	array
	(
		\HeimrichHannot\Collab\CollabConfig::OBSERVER_TASKS_FROM_MAILS => 'HeimrichHannot\Collab\Observer\TaskMailCreationObserver',
	)
);

/**
 * Observers
 */
array_insert(
	$GLOBALS['OBSERVER']['SUBJECTS'],
	1,
	array
	(
		\HeimrichHannot\Collab\CollabConfig::OBSERVER_SUBJECT_TASK => 'HeimrichHannot\Collab\Observer\TaskSubject',
	)
);

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('HeimrichHannot\Collab\Backend\BackendHooks', 'executePostActionsHook');

/**
 * Javascript
 */
if (TL_MODE == 'BE')
{
	$GLOBALS['TL_JAVASCRIPT']['collab-be'] =
		'system/modules/collab/assets/js/collab-be' . (!$GLOBALS['TL_CONFIG']['debugMode'] ? '.min' : '') . '.js' . (TL_MODE
																													 == 'BE' ? '' : '|static');;
	$GLOBALS['TL_CSS']['collab-be'] = 'system/modules/collab/assets/css/collab-be.css';
}


/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'tasklists';
$GLOBALS['TL_PERMISSIONS'][] = 'tasklistp';
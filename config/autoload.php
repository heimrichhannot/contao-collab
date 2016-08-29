<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Models
	'HeimrichHannot\Collab\TaskListModel'                       => 'system/modules/collab/models/TaskListModel.php',
	'HeimrichHannot\Collab\TaskModel'                           => 'system/modules/collab/models/TaskModel.php',

	// Observer
	'HeimrichHannot\Collab\Observer\TaskSubject'                => 'system/modules/collab/observer/subjects/TaskSubject.php',
	'HeimrichHannot\Collab\Observer\TaskMailCreationObserver'   => 'system/modules/collab/observer/observers/TaskMailCreationObserver.php',
	'HeimrichHannot\Collab\Observer\TaskRemoveAssigneeObserver' => 'system/modules/collab/observer/observers/TaskRemoveAssigneeObserver.php',
	'HeimrichHannot\Collab\Observer\TaskNotificationObserver'   => 'system/modules/collab/observer/observers/TaskNotificationObserver.php',
	'HeimrichHannot\Collab\Observer\TaskCreationObserver'       => 'system/modules/collab/observer/observers/TaskCreationObserver.php',

	// Classes
	'HeimrichHannot\Collab\CollabConfig'                        => 'system/modules/collab/classes/CollabConfig.php',
	'HeimrichHannot\Collab\Helper\Helper'                       => 'system/modules/collab/classes/helper/Helper.php',
	'HeimrichHannot\Collab\Helper\TaskHelper'                   => 'system/modules/collab/classes/helper/TaskHelper.php',
	'HeimrichHannot\Collab\Helper\TaskListHelper'               => 'system/modules/collab/classes/helper/TaskListHelper.php',
	'HeimrichHannot\Collab\Backend\BackendHooks'                => 'system/modules/collab/classes/backend/BackendHooks.php',
	'HeimrichHannot\Collab\Backend\TaskBackend'                 => 'system/modules/collab/classes/backend/TaskBackend.php',
	'HeimrichHannot\Collab\Backend\ObserverBackend'             => 'system/modules/collab/classes/backend/ObserverBackend.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'hint_collab' => 'system/modules/collab/templates/hints',
));

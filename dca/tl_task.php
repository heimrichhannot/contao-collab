<?php

$GLOBALS['TL_DCA']['tl_task'] = array(
	'config'      => array(
		'dataContainer'     => 'Table',
		'enableVersioning'  => true,
		'oncreate_callback' => array(
			array('HeimrichHannot\Collab\Backend\TaskBackend', 'setDefaults'),
		),
		'onload_callback'   => array(
			array('HeimrichHannot\Collab\Backend\TaskBackend', 'showHint'),
		),
		'onsubmit_callback' => array(
			array('HeimrichHannot\Haste\Dca\General', 'setDateAdded'),
		),
		'ondelete_callback' => array(
			array('HeimrichHannot\Collab\Backend\TaskBackend', 'deleteAttachments'),
		),
		'sql'               => array(
			'keys' => array(
				'id' => 'primary',
			),
		),
	),
	'list'        => array(
		'label'             => array(
			'fields'         => array('', 'id', 'title', 'assignee', 'tasklist', 'dateAdded', 'deadline'),
			'showColumns'    => true,
			'label_callback' => array('HeimrichHannot\Collab\Backend\TaskBackend', 'listItem'),
		),
		'sorting'           => array(
			'mode'        => 2,
			'fields'      => array('id DESC', 'dateAdded', 'deadline'),
			'panelLayout' => 'filter;sort,search,limit',
			'filter'      => \HeimrichHannot\Collab\Backend\TaskBackend::filterList(),
		),
		'global_operations' => array(
			'tasklist' => array(
				'label'      => &$GLOBALS['TL_LANG']['tl_task']['editLists'],
				'href'       => 'table=tl_tasklist',
				'icon'       => 'system/modules/collab/assets/img/icon-tasklist.png',
				'attributes' => 'onclick="Backend.getScrollOffset();"',
			),
			'all'      => array(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();"',
			),
		),
		'operations'        => array(
			'edit'   => array(
				'label' => &$GLOBALS['TL_LANG']['tl_task']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif',
			),
			'copy'   => array(
				'label' => &$GLOBALS['TL_LANG']['tl_task']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif',
			),
			'delete' => array(
				'label'      => &$GLOBALS['TL_LANG']['tl_task']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array(
				'label'           => &$GLOBALS['TL_LANG']['tl_task']['toggle'],
				'icon'            => 'visible.gif',
				'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback' => array('HeimrichHannot\Collab\Backend\TaskBackend', 'toggleIcon'),
			),
			'show'   => array(
				'label' => &$GLOBALS['TL_LANG']['tl_task']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif',
			),
		),
	),
	'palettes'    => array(
		'__selector__' => array('type', 'published'),
		'default'      => '{general_legend},complete,title,deadline,tasklist;{assignee_legend},assigneeType,assignee;{info_legend},description,attachments;{author_legend:hide},authorType,author;{publish_legend},published;',
		'mail'         => '{general_legend},complete,title,deadline,tasklist;{assignee_legend},assigneeType,assignee;{info_legend},fromName,fromEmail,description,attachments;{author_legend:hide},authorType,author;{publish_legend},published;',
	),
	'subpalettes' => array(
		'published' => 'start,stop',
	),
	'fields'      => array(
		'id'           => array(
			'label'   => &$GLOBALS['TL_LANG']['tl_task']['id'],
			'flag'    => 12,
			'sorting' => true,
			'sql'     => "int(10) unsigned NOT NULL auto_increment",
		),
		'tasklist'     => array(
			'label'            => &$GLOBALS['TL_LANG']['tl_task']['tasklist'],
			'exclude'          => true,
			'inputType'        => 'select',
			'foreignKey'       => 'tl_tasklist.title',
			'filter'           => true,
			'options_callback' => array('HeimrichHannot\Collab\Backend\TaskBackend', 'getTaskLists'),
			'sql'              => "int(10) unsigned NOT NULL default '0'",
			'eval'             => array('tl_class' => 'w50', 'includeBlankOption' => true, 'findInSet' => true),
			'relation'         => array('type' => 'belongsTo', 'load' => 'eager'),
		),
		'tstamp'       => array(
			'label' => &$GLOBALS['TL_LANG']['tl_task']['tstamp'],
			'sql'   => "int(10) unsigned NOT NULL default '0'",
		),
		'type'         => array(
			'label'            => &$GLOBALS['TL_LANG']['tl_observer']['type'],
			'exclude'          => true,
			'default'          => \HeimrichHannot\Collab\CollabConfig::TASK_TYPE_DEFAULT,
			'inputType'        => 'hidden',
			'options_callback' => array('HeimrichHannot\Collab\Backend\TaskBackend', 'getTaskTypes'),
			'reference'        => &$GLOBALS['TL_LANG']['TASKS'],
			'sql'              => "varchar(32) NOT NULL default ''",
		),
		'dateAdded'    => array(
			'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
			'sorting' => true,
			'flag'    => 6,
			'eval'    => array('rgxp' => 'datim', 'doNotCopy' => true),
			'sql'     => "int(10) unsigned NOT NULL default '0'",
		),
		'title'        => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_task']['title'],
			'exclude'   => true,
			'search'    => true,
			'sorting'   => true,
			'flag'      => 1,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'authorType'   => array(
			'label'            => &$GLOBALS['TL_LANG']['tl_task']['authorType'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\Collab\Backend\TaskBackend', 'getAuthorTypes'),
			'reference'        => $GLOBALS['TL_LANG']['tl_task']['reference']['authorType'],
			'eval'             => array(
				'doNotCopy'          => true,
				'submitOnChange'     => true,
				'tl_class'           => 'w50 clr',
				'includeBlankOption' => true,
			),
			'sql'              => "varchar(255) NOT NULL default ''",
		),
		'author'       => array(
			'label'            => &$GLOBALS['TL_LANG']['tl_task']['author'],
			'exclude'          => true,
			'search'           => true,
			'filter'           => true,
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\Collab\Backend\TaskBackend', 'getAuthorAsOptions'),
			'eval'             => array(
				'doNotCopy'          => true,
				'chosen'             => true,
				'includeBlankOption' => true,
				'tl_class'           => 'w50',
			),
			'sql'              => "int(10) unsigned NOT NULL default '0'",
		),
		'assigneeType' => array(
			'label'            => &$GLOBALS['TL_LANG']['tl_task']['assigneeType'],
			'exclude'          => true,
			'filter'           => true,
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\Collab\Backend\TaskBackend', 'getAuthorTypes'),
			'reference'        => $GLOBALS['TL_LANG']['tl_task']['reference']['authorType'],
			'eval'             => array(
				'doNotCopy'          => true,
				'submitOnChange'     => true,
				'tl_class'           => 'w50 clr',
				'includeBlankOption' => true,
			),
			'sql'              => "varchar(255) NOT NULL default ''",
		),
		'assignee'     => array(
			'label'            => &$GLOBALS['TL_LANG']['tl_task']['assignee'],
			'exclude'          => true,
			'search'           => true,
			'filter'           => true,
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\Collab\Backend\TaskBackend', 'getAssigneesAsOptions'),
			'eval'             => array(
				'doNotCopy'          => true,
				'chosen'             => true,
				'includeBlankOption' => true,
				'tl_class'           => 'w50',
			),
			'sql'              => "int(10) unsigned NOT NULL default '0'",
		),
		'deadline'     => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_task']['deadline'],
			'exclude'   => true,
			'sorting'   => true,
			'flag'      => 6,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''",
		),
		'description'  => array(
			'label'       => &$GLOBALS['TL_LANG']['tl_task']['description'],
			'exclude'     => true,
			'search'      => true,
			'inputType'   => 'textarea',
			'eval'        => array('rte' => 'tinyMCE', 'helpwizard' => true),
			'explanation' => 'insertTags',
			'sql'         => "mediumtext NULL",
		),
		'attachments'  => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_task']['attachments'],
			'exclude'   => true,
			'inputType' => 'multifileupload',
			'eval'      => array(
				'explanation'    => &$GLOBALS['TL_LANG']['tl_submission']['attachmentsExplanation'],
				'tl_class'       => 'clr',
				'filesOnly'      => true,
				'fieldType'      => 'checkbox',
				'extensions'     => \Config::get('uploadTypes'),
				'maxUploadSize'  => 10,
				'uploadFolder'   => \HeimrichHannot\Collab\CollabConfig::getDefaultAttachmentSRC(),
				'addRemoveLinks' => true,
				'multiple'       => true,
			),
			'sql'       => "blob NULL",
		),
		'fromName'     => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_task']['fromName'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'fromEmail'    => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_task']['fromEmail'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('tl_class' => 'w50', 'rgxp' => 'mail'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'complete'     => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_task']['complete'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => array('doNotCopy' => true, 'submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'published'    => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_task']['published'],
			'exclude'   => true,
			'filter'    => true,
			'default'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('doNotCopy' => true, 'submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default '1'",
		),
		'start'        => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_task']['start'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''",
		),
		'stop'         => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_task']['stop'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''",
		),
	),
);

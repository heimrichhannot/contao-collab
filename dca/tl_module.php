<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$arrDca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Selector
 */
$arrDca['palettes']['__selector__'][] = 'createTask';

/**
 * Subpalettes
 */
$arrDca['subpalettes']['createTask'] = 'tasklist';

/**
 * Fields
 */
$arrFields = array(
	'createTask' => array(
		'label'     => &$GLOBALS['TL_LANG']['tl_module']['createTask'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('tl_class' => 'w50 clr', 'submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''",
	),
	'tasklist'   => array(
		'label'            => &$GLOBALS['TL_LANG']['tl_module']['tasklist'],
		'exclude'          => true,
		'inputType'        => 'select',
		'options_callback' => array('HeimrichHannot\Collab\Backend\ModuleBackend', 'getTaskLists'),
		'sql'              => "int(10) unsigned NOT NULL default '0'",
		'eval'             => array('tl_class' => 'w50 clr', 'includeBlankOption' => true),
	),
);

$arrDca['fields'] = array_merge($arrDca['fields'], $arrFields);
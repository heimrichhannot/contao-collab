<?php

/**
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] = str_replace('fop;', 'fop;{collab_legend},tasklists,tasklistp;', $GLOBALS['TL_DCA']['tl_user_group']['palettes']['default']);


/**
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['tasklists'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['tasklists'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_tasklist.title',
	'eval'                    => array('multiple' => true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_user_group']['fields']['tasklistp'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['tasklistp'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('create', 'delete'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple' => true),
	'sql'                     => "blob NULL"
);
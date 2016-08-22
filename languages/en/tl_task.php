<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_task'];

/**
 * Fields
 */
$arrLang['id']           = array('ID', '');
$arrLang['tstamp']       = array('Revision date', '');
$arrLang['title']        = array('Title', 'Please enter a title.');
$arrLang['deadline']     = array('Deadline', 'Enter the deadline of the task.');
$arrLang['tasklist']     = array('Tasklist', 'Select a tasklist if you want to group the tasks.');
$arrLang['authorType']   = array('Author type', 'Select the type of the author model.');
$arrLang['author']       = array('Author', 'Select the author.');
$arrLang['assigneeType'] = array('Assignee type', 'Select the type of the assignee model.');
$arrLang['assignee']     = array('Assignee', 'Select the assignee.');
$arrLang['fromName']     = array('From name', 'The name of the email sender.');
$arrLang['fromEmail']    = array('From email', 'The email of the email sender.');
$arrLang['description']  = array('Description', 'You can use HTML tags to format the text.');
$arrLang['attachments']  = array('Attachments', 'Add attachments to the task.');
$arrLang['complete']     = array('Complete', 'Complete the task.');
$arrLang['published']    = array('Publish task', 'Make the task publicly visible on the website.');
$arrLang['start']        = array('Show from', 'Do not publish the task on the website before this date.');
$arrLang['stop']         = array('Show until', 'Unpublish the task on the website after this date.');


/**
 * Legends
 */
$arrLang['general_legend'] = 'General';
$arrLang['author_legend']    = 'Author';
$arrLang['assignee_legend']    = 'Assignee';
$arrLang['info_legend']    = 'Information';
$arrLang['publish_legend'] = 'Publish settings';


/**
 * Buttons
 */
$arrLang['new']       = array('New task', 'Task create');
$arrLang['edit']      = array('Edit task', 'Edit task ID %s');
$arrLang['copy']      = array('Duplicate task', 'Duplicate task ID %s');
$arrLang['delete']    = array('Delete task', 'Delete task ID %s');
$arrLang['toggle']    = array('Publish/unpublish task', 'Publish/unpublish task ID %s');
$arrLang['show']      = array('Task details', 'Show the details of task ID %s');
$arrLang['editLists'] = array('Manage tasklists', 'Manage tasklists that task will be delegated to.');

/**
 * Misc
 */
$arrLang['toggleTaskTitle'] = 'Complete / Re-open task';

/**
 * Reference
 */

$arrLang['reference']['authorType'][\HeimrichHannot\Collab\CollabConfig::AUTHOR_TYPE_NONE]   = 'None';
$arrLang['reference']['authorType'][\HeimrichHannot\Collab\CollabConfig::AUTHOR_TYPE_MEMBER] = 'Member';
$arrLang['reference']['authorType'][\HeimrichHannot\Collab\CollabConfig::AUTHOR_TYPE_USER]   = 'User';
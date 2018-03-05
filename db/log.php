<?php

/**
 * Definition of log events.
 *
 * NOTE: this is an example how to insert log event during installation/update.
 * It is not really essential to know about it, but these logs were created as example
 * in the previous 1.9 NEWMODULE.
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $DB;

$logs = array(
    array('module' => 'amcquiz', 'action' => 'add', 'mtable' => 'amcquiz', 'field' => 'name'),
    array('module' => 'amcquiz', 'action' => 'update', 'mtable' => 'amcquiz', 'field' => 'name'),
    array('module' => 'amcquiz', 'action' => 'view', 'mtable' => 'amcquiz', 'field' => 'name'),
    array('module' => 'amcquiz', 'action' => 'view all', 'mtable' => 'amcquiz', 'field' => 'name'),
);

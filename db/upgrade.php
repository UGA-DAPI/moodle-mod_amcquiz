<?php

/**
 * This file keeps track of upgrades to the automultiplechoice module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package    mod_amcquiz
 * @copyright  2013-2014 Silecs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute amcquiz upgrade from the given old version
 * http://docs.moodle.org/dev/XMLDB_creating_new_DDL_functions
 * @param int $oldversion
 * @return bool
 */
function xmldb_amcquiz_upgrade($oldversion) {
    global $DB;

    if (version_compare(phpversion(), '5.6.4') < 0) {
        error("This module uses MOODLE_33 and requires PHP 5.6.4 (your current version is ".phpversion().") and higher. It might not work properly.");
    }


    return true;
}

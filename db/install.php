<?php

/**
 * This file replaces the legacy STATEMENTS section in db/install.xml,
 * lib.php/modulename_install() post installation hook and partially defaults.php
 *
 * @package    mod_amcquiz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Post installation procedure
 *
 * @see upgrade_plugins_modules()
 */
function xmldb_amcquiz_install() {
    if (version_compare(phpversion(), '5.6.4') < 0) {
        error("This module uses MOODLE_33 and requires PHP 5.6.4 (your current version is ".phpversion().") and higher. It might not work properly.");
    }
}

/**
 * Post installation recovery procedure
 *
 * @see upgrade_plugins_modules()
 */
function xmldb_amcquiz_install_recovery() {
}

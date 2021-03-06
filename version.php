<?php

/**
 * Defines the version of automultiplechoice
 *
 * This code fragment is called by moodle_needs_upgrading() and
 * /admin/index.php
 *
 * @package    mod_amcquiz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2017122110;      // The current module version (Date: YYYYMMDDXX)
$plugin->requires  = 2017051502;      // Requires this Moodle version
$plugin->cron      = 0;               // Period for cron to check this module (secs)
$plugin->component = 'mod_amcquiz'; // To check on upgrade, that module sits in correct place

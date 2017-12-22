<?php

/**
 * This file define the form for the global plugin configuration
 * ie moodle->admin->plugin->amcquiz->settings
 */

/* @var $ADMIN admin_root */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    //require_once($CFG->dirroot.'/mod/amcquiz/locallib.php');


    $s = new admin_setting_configtext(
        'amccodelength',
        get_string('settings_code_length_short', 'mod_amcquiz'),
        get_string('settings_code_length_full', 'mod_amcquiz'),
        8,
        PARAM_INT
    );
    $s->plugin = 'mod_amcquiz';
    $settings->add($s);

    $s = new admin_setting_configtext(
        'instructionslstudent',
        get_string('settings_instructionslstudent_short', 'mod_amcquiz'),
        get_string('settings_instructionslstudent_full', 'mod_amcquiz'),
        get_string('settings_instructionslstudent_default', 'mod_amcquiz'),
        PARAM_TEXT
    );
    $s->plugin = 'mod_amcquiz';
    $settings->add($s);

    $s = new admin_setting_configtext(
        'instructionslnamestd',
        get_string('settings_instructionslnamestd_short', 'mod_amcquiz'),
        get_string('settings_instructionslnamestd_full', 'mod_amcquiz'),
        get_string('settings_instructionslnamestd_default', 'mod_amcquiz'),
        PARAM_TEXT
    );
    $s->plugin = 'mod_amcquiz';
    $settings->add($s);

    $s = new admin_setting_configtext(
        'instructionslnameanon',
        get_string('settings_instructionslnameanon_short', 'mod_amcquiz'),
        get_string('settings_instructionslnameanon_full', 'mod_amcquiz'),
        '',
        PARAM_TEXT
    );
    $s->plugin = 'mod_amcquiz';
    $settings->add($s);

    $s = new admin_setting_configtextarea(
        'instructions',
        get_string('settings_instructions_short', 'mod_amcquiz'),
        get_string('settings_instructions_full', 'mod_amcquiz'),
        get_string('settings_instructions_default', 'mod_amcquiz'),
        PARAM_RAW
    );
    /*    $s = new admin_setting_configtextarea(
        'instructions',
        get_string('settings_instructions_short', 'mod_amcquiz'),
        get_string('settings_instructions_full', 'mod_amcquiz'),
        '<h1>TITI</h1>',
        PARAM_RAW
    );*/
    $s->plugin = 'mod_amcquiz';
    $settings->add($s);

    $s = new admin_setting_configtextarea(
        'scoringrules',
        'Scoring rules',
        get_string('settings_scoring_rules', 'mod_amcquiz'),
        get_string('settings_scoring_rules_default', 'mod_amcquiz'),
        PARAM_TEXT
    );
    $s->plugin = 'mod_amcquiz';
    $settings->add($s);
  /*  $s = new admin_setting_configtextarea(
        'scoringrules',
        'Scoring rules',
        get_string('settings_scoring_rules', 'mod_amcquiz'),
        'tata',
        PARAM_TEXT
    );

*/
    $s = new admin_setting_configtextarea(
        'idnumberprefixes',
        get_string('settings_idnumberprefixes_short', 'mod_amcquiz'),
        get_string('settings_idnumberprefixes_full', 'mod_amcquiz'),
        '',
        PARAM_TEXT
    );
    $s->plugin = 'mod_amcquiz';
    $settings->add($s);

}

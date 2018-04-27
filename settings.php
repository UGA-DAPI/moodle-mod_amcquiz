<?php

/**
 * This file define the form for the global plugin configuration
 * ie moodle->admin->plugin->amcquiz->settings.
 */

/* @var $ADMIN admin_root */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext(
        'mod_amcquiz/apiurl',
        get_string('settings_amcquiz_apiurl_short', 'mod_amcquiz'),
        get_string('settings_amcquiz_apiurl_full', 'mod_amcquiz'),
        'http://bbb.u-ga.fr/V1/amc/',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'mod_amcquiz/apiglobalkey',
        get_string('settings_amcquiz_apiglobalkey_short', 'mod_amcquiz'),
        get_string('settings_amcquiz_apiglobalkey_full', 'mod_amcquiz'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'mod_amcquiz/amccodelength',
        get_string('settings_code_length_short', 'mod_amcquiz'),
        get_string('settings_code_length_full', 'mod_amcquiz'),
        8,
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        'mod_amcquiz/instructionslstudent',
        get_string('settings_instructionslstudent_short', 'mod_amcquiz'),
        get_string('settings_instructionslstudent_full', 'mod_amcquiz'),
        get_string('settings_instructionslstudent_default', 'mod_amcquiz'),
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'mod_amcquiz/instructionslnamestd',
        get_string('settings_instructionslnamestd_short', 'mod_amcquiz'),
        get_string('settings_instructionslnamestd_full', 'mod_amcquiz'),
        get_string('settings_instructionslnamestd_default', 'mod_amcquiz'),
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'mod_amcquiz/instructionslnameanon',
        get_string('settings_instructionslnameanon_short', 'mod_amcquiz'),
        get_string('settings_instructionslnameanon_full', 'mod_amcquiz'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtextarea(
        'mod_amcquiz/instructions',
        get_string('settings_instructions_short', 'mod_amcquiz'),
        '',
        get_string('settings_instructions_default', 'mod_amcquiz'),
        PARAM_RAW
    ));

    $settings->add(new admin_setting_configtextarea(
        'mod_amcquiz/scoringrules',
        get_string('settings_scoring_rules', 'mod_amcquiz'),
        get_string('settings_scoring_rules_help', 'mod_amcquiz'),
        get_string('settings_scoring_rules_default', 'mod_amcquiz'),
        PARAM_TEXT
    ));
}

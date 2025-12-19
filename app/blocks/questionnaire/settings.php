<?php

defined('MOODLE_INTERNAL') || die;
if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configtext(
        'block_questionnaire/link',
        get_string('link', 'block_questionnaire'),
        get_string('link_help', 'block_questionnaire'),
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'block_questionnaire/title',
        get_string('title', 'block_questionnaire'),
        get_string('title_help', 'block_questionnaire'),
        '',
        PARAM_TEXT
    ));
    //--1
    $settings->add(new admin_setting_configtext(
        'block_questionnaire/link1',
        get_string('link', 'block_questionnaire'),
        get_string('link_help', 'block_questionnaire'),
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'block_questionnaire/title1',
        get_string('title', 'block_questionnaire'),
        get_string('title_help', 'block_questionnaire'),
        '',
        PARAM_TEXT
    ));
    //--2
    $settings->add(new admin_setting_configtext(
        'block_questionnaire/link2',
        get_string('link', 'block_questionnaire'),
        get_string('link_help', 'block_questionnaire'),
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'block_questionnaire/title2',
        get_string('title', 'block_questionnaire'),
        get_string('title_help', 'block_questionnaire'),
        '',
        PARAM_TEXT
    ));
}

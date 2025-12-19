<?php

defined('MOODLE_INTERNAL') || die;

function xmldb_local_cdo_vkr_install(): bool
{
    global $DB;

    $dbman = $DB->get_manager();
    // Define table local_cdo_vkr_file_of_vkr to be created.
    $table = new xmldb_table('local_cdo_vkr_file_of_vkr');

    // Adding fields to table local_cdo_vkr_file_of_vkr.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('id_vkr', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
    $table->add_field('file_id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
    $table->add_field('type', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
    $table->add_field('commentary', XMLDB_TYPE_TEXT, null, null, null, null, '');

    // Adding keys to table local_cdo_vkr_file_of_vkr.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

    // Conditionally launch create table for local_cdo_vkr_file_of_vkr.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Define table local_cdo_vkr_vkrs to be created.
    $table = new xmldb_table('local_cdo_vkr_vkrs');

    // Adding fields to table local_cdo_vkr_vkrs.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('fio', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('name_of_vkr', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('manager', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('years', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('edu_group', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('edu_division', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('edu_level', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('edu_form', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('edu_speciality', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('grade', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('status_id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('status_changed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('agreedebs', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
    $table->add_field('acquainted', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
    $table->add_field('user_id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
    $table->add_field('manager_id', XMLDB_TYPE_INTEGER, '11', null, null, null, null);

    // Adding keys to table local_cdo_vkr_vkrs.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
    $table->add_key('user_id_user', XMLDB_KEY_FOREIGN, ['user_id'], 'user', ['id']);

    // Conditionally launch create table for local_cdo_vkr_vkrs.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    return true;
}
<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Custom code to be run on installing the plugin.
 */
function xmldb_block_slider_install()
{

    global $DB;
    $dbman = $DB->get_manager();

    // Define table block_slider to be created.
    $table = new xmldb_table('block_slider');

    // Adding fields to table block_slider.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('file_id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
    $table->add_field('slide_text', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('slide_header', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

    // Adding keys to table block_slider.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

    // Conditionally launch create table for block_slider.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    return true;
}

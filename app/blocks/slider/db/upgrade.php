<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_block_slider_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016052302) {

        // Define field id to be added to block_slider.
        $table = new xmldb_table('block_slider');
        $field = new xmldb_field('section', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'slide_text');
        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Slider savepoint reached.
        upgrade_block_savepoint(true, 2016052302, 'slider');
    }

    if ($oldversion < 2016052304) {

        // Define field text_company to be added to block_slider.
        $table = new xmldb_table('block_slider');
        $field = new xmldb_field('text_company', XMLDB_TYPE_TEXT, null, null, null, null, null, 'section');

        // Conditionally launch add field text_company.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('email', XMLDB_TYPE_TEXT, null, null, null, null, null, 'section');

        // Conditionally launch add field email.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('telephone', XMLDB_TYPE_TEXT, null, null, null, null, null, 'section');

        // Conditionally launch add field telephone.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Slider savepoint reached.
        upgrade_block_savepoint(true, 2016052304, 'slider');
    }

    return true;
}


<?php
/**
 * Plugin installation script
 *
 * @package    local_cdo_ok
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute plugin installation
 *
 * @return bool
 * @throws coding_exception
 */
function xmldb_local_yourpluginname_install() {
    // Perform plugin installation tasks here

    // Example: Create a new table in the Moodle database
    $table = new xmldb_table('yourplugin_table');
    $field1 = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
    $field2 = new xmldb_field('name', XMLDB_TYPE_CHAR, '100', null, null, null, null, null, null, null);
    $field3 = new xmldb_field('description', XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, null);

    // Add fields to the table
    $table->addField($field1);
    $table->addField($field2);
    $table->addField($field3);

    // Create the table if it doesn't exist already
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Example: Insert data into the newly created table
    $record = new stdClass();
    $record->name = 'Plugin Name';
    $record->description = 'Plugin Description';
    $record->id = $DB->insert_record('yourplugin_table', $record);

    /**
     * Save release number and other metadata information
     * Note: You should update these values accordingly
     */
    plugin_release_version('local/yourpluginname', '1.0.0');
    return true;
}
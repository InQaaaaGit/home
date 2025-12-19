<?php

use tool_cdo_config\roles\role_factory;

defined('MOODLE_INTERNAL') || die();

function xmldb_tool_cdo_config_upgrade($oldversion): bool
{
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 20221024.06) {

        // Define field port to be added to cdo_config.
        $table = new xmldb_table('cdo_config');
        $field = new xmldb_field('port', XMLDB_TYPE_TEXT, null, null, null, null, null, 'endpoint');

        // Conditionally launch add field port.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cdo_config savepoint reached.
        upgrade_plugin_savepoint(true, 20221024.06, 'tool', 'cdo_config');
    }

    $role_factory = new role_factory();
    $role_factory->create_roles();
    return true;
}

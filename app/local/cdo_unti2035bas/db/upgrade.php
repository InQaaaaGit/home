<?php

defined('MOODLE_INTERNAL') || die();


function xmldb_local_cdo_unti2035bas_upgrade(int $oldversion): bool {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025061603) {
        $table = new xmldb_table('cdo_unti2035bas_stream');
        $fields = [
            new xmldb_field('ismanual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0'),
            new xmldb_field('name', XMLDB_TYPE_TEXT, null, null, null, null, null),
            new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null),
        ];
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        $table = new xmldb_table('cdo_unti2035bas_block');
        $fields = [
            new xmldb_field('ismanual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0'),
            new xmldb_field('name', XMLDB_TYPE_TEXT, null, null, null, null, null),
            new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null),
        ];
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        $table = new xmldb_table('cdo_unti2035bas_module');
        $fields = [
            new xmldb_field('ismanual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0'),
            new xmldb_field('name', XMLDB_TYPE_TEXT, null, null, null, null, null),
            new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null),
        ];
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        $table = new xmldb_table('cdo_unti2035bas_theme');
        $fields = [
            new xmldb_field('ismanual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0'),
            new xmldb_field('name', XMLDB_TYPE_TEXT, null, null, null, null, null),
            new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null),
        ];
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        $table = new xmldb_table('cdo_unti2035bas_activity');
        $fields = [
            new xmldb_field('ismanual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0'),
            new xmldb_field('name', XMLDB_TYPE_TEXT, null, null, null, null, null),
            new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null),
        ];
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        $table = new xmldb_table('cdo_unti2035bas_assessment');
        $fields = [
            new xmldb_field('ismanual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0'),
            new xmldb_field('name', XMLDB_TYPE_TEXT, null, null, null, null, null),
            new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null),
        ];
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        upgrade_plugin_savepoint(true, 2025061603, 'local', 'cdo_unti2035bas');
    }

    if ($oldversion < 2025060206) {
        // Define table cdo_unti2035bas_xapi_sent to be created.
        $table = new xmldb_table('cdo_unti2035bas_xapi_sent');

        // Adding fields to table cdo_unti2035bas_xapi_sent.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrid', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('statement_id', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('statement_type', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'video_watched');
        $table->add_field('progress_value', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, null);
        $table->add_field('duration_seconds', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table cdo_unti2035bas_xapi_sent.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('user_module_lrid', XMLDB_KEY_UNIQUE, ['userid', 'cmid', 'lrid']);
        $table->add_key('statement_id_uk', XMLDB_KEY_UNIQUE, ['statement_id']);

        // Adding indexes to table cdo_unti2035bas_xapi_sent.
        $table->add_index('userid_idx', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        $table->add_index('cmid_idx', XMLDB_INDEX_NOTUNIQUE, ['cmid']);
        $table->add_index('timecreated_idx', XMLDB_INDEX_NOTUNIQUE, ['timecreated']);

        // Conditionally launch create table for cdo_unti2035bas_xapi_sent.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cdo_unti2035bas savepoint reached.
        upgrade_plugin_savepoint(true, 2025060206, 'local', 'cdo_unti2035bas');
    }

    if ($oldversion < 2025061810) {
        // Define table cdo_unti2035bas_xapi_results to be created.
        $table = new xmldb_table('cdo_unti2035bas_xapi_results');

        // Adding fields to table cdo_unti2035bas_xapi_results.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('result', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('query', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table cdo_unti2035bas_xapi_results.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table cdo_unti2035bas_xapi_results.
        // $table->add_index('id_idx', XMLDB_INDEX_NOTUNIQUE, ['id']);

        // Conditionally launch create table for cdo_unti2035bas_xapi_results.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cdo_unti2035bas savepoint reached.
        upgrade_plugin_savepoint(true, 2025061810, 'local', 'cdo_unti2035bas');
    }

    if ($oldversion < 2025090501) {
        $table = new xmldb_table('cdo_unti2035bas_stream');
        $fields = [
            new xmldb_field('fdextensions', XMLDB_TYPE_TEXT, null, null, false, null, null),
        ];
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
        $DB->execute("UPDATE {cdo_unti2035bas_stream} SET fdextensions='[]' WHERE fdextensions IS NULL");
        upgrade_plugin_savepoint(true, 2025090501, 'local', 'cdo_unti2035bas');
    }

    if ($oldversion < 2025090701) {
        $table = new xmldb_table('cdo_unti2035bas_activity');
        $fields = [
            new xmldb_field('resultcomparability', XMLDB_TYPE_INTEGER, '10', null, false),
            new xmldb_field('admittanceform', XMLDB_TYPE_CHAR, '50', null, false),
        ];
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
        $DB->execute("UPDATE {cdo_unti2035bas_activity} SET resultcomparability=1 WHERE resultcomparability IS NULL");
        $DB->execute(
            "UPDATE {cdo_unti2035bas_activity} SET admittanceform='offline' WHERE admittanceform IS NULL and type_ = 'practice'"
        );
        upgrade_plugin_savepoint(true, 2025090701, 'local', 'cdo_unti2035bas');
    }

    if ($oldversion < 2025091001) {
        $table = new xmldb_table('cdo_unti2035bas_factdef');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('lrid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('streamid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('baseobject', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('baseobjectid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timestamp', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('changed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('version', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timesent', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('resultextensions', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextextensions', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('baseobject_idx', XMLDB_KEY_UNIQUE, ['baseobject', 'baseobjectid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025091001, 'local', 'cdo_unti2035bas');
    }

    if ($oldversion < 2025091402) {
        $table = new xmldb_table('cdo_unti2035bas_fact');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('lrid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('streamid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('factdefid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('actoruntiid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timestamp', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timesent', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('resultscoreraw', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('resultscoremin', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('resultscoremax', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('resultscoretarget', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('resultsuccess', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('resultduration', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('resultattemptsmax', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('resultattemptnum', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('resultextensions', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextextensions', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('factactorattempt_uk', XMLDB_KEY_UNIQUE, ['factdefid', 'actoruntiid', 'resultattemptnum']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025091402, 'local', 'cdo_unti2035bas');
    }

    if ($oldversion < 2025091702) {
        $table = new xmldb_table('cdo_unti2035bas_factdef');
        $fields = [
            new xmldb_field('instructoruntiid', XMLDB_TYPE_INTEGER, '10', null, false),
        ];
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
        $table = new xmldb_table('cdo_unti2035bas_fact');
        $fields = [
            new xmldb_field('instructoruntiid', XMLDB_TYPE_INTEGER, '10', null, false),
        ];
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
        upgrade_plugin_savepoint(true, 2025091702, 'local', 'cdo_unti2035bas');
    }

    if ($oldversion < 2025092302) {

        $table = new xmldb_table('cdo_unti2035bas_diary');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('lrid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('streamid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('actoruntiid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timestamp', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timesent', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('diaryfiles3url', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('diaryfilemimetype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('diaryfilefilesize', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('diaryfilesha256', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('diaryfiletimeupload', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('streamactor_uk', XMLDB_KEY_UNIQUE, ['streamid', 'actoruntiid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025092302, 'local', 'cdo_unti2035bas');
    }

    return true;
}

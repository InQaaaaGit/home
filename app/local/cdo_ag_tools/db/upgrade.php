<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_local_cdo_ag_tools_upgrade($oldversion): bool
{
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2023121412) {

        // Define table cdo_ag_tools_user_files to be created.
        $table = new xmldb_table('cdo_ag_tools_user_files');

        // Adding fields to table cdo_ag_tools_user_files.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fileid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table cdo_ag_tools_user_files.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for cdo_ag_tools_user_files.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // cdo_ag_tools savepoint reached.
        upgrade_plugin_savepoint(true, 2023121412, 'local', 'cdo_ag_tools');
    }
    if ($oldversion < 2023121423) {
        // Создаем таблицу для хранения уведомлений об оценках
        $table = new xmldb_table('local_cdo_ag_grade_notifications');

        // Добавляем поля
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('grade', XMLDB_TYPE_NUMBER, '10,2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('modulename', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('moduletype', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Добавляем ключи
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

        // Создаем таблицу
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2023121423, 'local', 'cdo_ag_tools');
    }

    if ($oldversion < 2023121429) {
        // Создаем новую версию таблицы для синхронизации оценок с 1C
        $table = new xmldb_table('local_cdo_ag_tools_grades_1c');

        // Проверяем, существует ли уже таблица (может быть создана в install.xml)
        if (!$dbman->table_exists($table)) {
            // Добавляем поля
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('grade', XMLDB_TYPE_NUMBER, '10,5', null, XMLDB_NOTNULL, null, null);
            $table->add_field('section_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
            $table->add_field('item_type', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
            $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

            // Добавляем ключи
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

            // Добавляем индексы
            $table->add_index('course_user_idx', XMLDB_INDEX_NOTUNIQUE, ['course_id', 'user_id']);
            $table->add_index('user_idx', XMLDB_INDEX_NOTUNIQUE, ['user_id']);
            $table->add_index('course_idx', XMLDB_INDEX_NOTUNIQUE, ['course_id']);
            $table->add_index('section_idx', XMLDB_INDEX_NOTUNIQUE, ['section_id']);
            $table->add_index('created_at_idx', XMLDB_INDEX_NOTUNIQUE, ['created_at']);

            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2023121429, 'local', 'cdo_ag_tools');
    }

    if ($oldversion < 2023121436) {
        // Создаем таблицу для отслеживания уведомлений о работах
        $table = new xmldb_table('local_cdo_ag_work_notif');

        if (!$dbman->table_exists($table)) {
            // Добавляем поля
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('assignmentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('submissionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('upload_notified', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('grade_notified', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

            // Добавляем ключи
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

            // Добавляем индексы
            $table->add_index('userid_idx', XMLDB_INDEX_NOTUNIQUE, ['userid']);
            $table->add_index('courseid_idx', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
            $table->add_index('assignmentid_idx', XMLDB_INDEX_NOTUNIQUE, ['assignmentid']);
            $table->add_index('submissionid_idx', XMLDB_INDEX_NOTUNIQUE, ['submissionid']);
            $table->add_index('user_submission_idx', XMLDB_INDEX_NOTUNIQUE, ['userid', 'submissionid']);

            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2023121436, 'local', 'cdo_ag_tools');
    }

    return true;
}

<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade script for local_videoprogress plugin
 *
 * @param int $oldversion The version we are upgrading from
 * @return bool Success status
 */
function xmldb_local_videoprogress_upgrade($oldversion): bool
{
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024031507) {
        // Удаляем поле segments, так как оно больше не используется
        $table = new xmldb_table('local_videoprogress');
        $field = new xmldb_field('segments', XMLDB_TYPE_TEXT, null, null, null, null, null, 'progress');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Устанавливаем значение по умолчанию для поля progress
        $field = new xmldb_field('progress', XMLDB_TYPE_FLOAT, null, null, XMLDB_NOTNULL, null, '0', 'videoid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024031507, 'local', 'videoprogress');
    }

    if ($oldversion < 2024031508) {
        // Добавляем поле для хранения просмотренных сегментов
        $table = new xmldb_table('local_videoprogress');
        
        // Добавляем поле segments для хранения просмотренных сегментов в JSON формате
        $field = new xmldb_field('segments', XMLDB_TYPE_TEXT, null, null, null, null, null, 'progress');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
        // Добавляем поле duration для хранения общей длительности видео
        $field = new xmldb_field('duration', XMLDB_TYPE_FLOAT, null, null, null, null, '0', 'segments');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024031508, 'local', 'videoprogress');
    }

    return true;
} 
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

/**
 * Upgrade script for local_cdo_ok plugin
 *
 * @package    local_cdo_ok
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Функция обновления плагина
 *
 * @param int $oldversion Текущая версия плагина
 * @return bool
 */
function xmldb_local_cdo_ok_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025120102) {
        // Добавляем индексы для оптимизации производительности запросов
        
        // Таблица local_cdo_ok - индексы для частых запросов
        $table = new xmldb_table('local_cdo_ok');
        
        $index = new xmldb_index('group_tab_visible_idx', XMLDB_INDEX_NOTUNIQUE, ['group_tab', 'visible']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
        $index = new xmldb_index('sort_idx', XMLDB_INDEX_NOTUNIQUE, ['sort']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
        // Таблица local_cdo_ok_answer - индексы для JOIN операций
        $table = new xmldb_table('local_cdo_ok_answer');
        
        $index = new xmldb_index('user_question_integration_idx', XMLDB_INDEX_NOTUNIQUE, 
            ['user_id', 'question_id', 'integration']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
        $index = new xmldb_index('question_id_idx', XMLDB_INDEX_NOTUNIQUE, ['question_id']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
        $index = new xmldb_index('user_id_idx', XMLDB_INDEX_NOTUNIQUE, ['user_id']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
        // Таблица local_cdo_ok_active_group - индекс для JOIN
        $table = new xmldb_table('local_cdo_ok_active_group');
        
        $index = new xmldb_index('group_tab_active_idx', XMLDB_INDEX_NOTUNIQUE, ['group_tab', 'active']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
        // Таблица local_cdo_ok_confirm_answers - индекс для частых запросов
        $table = new xmldb_table('local_cdo_ok_confirm_answers');
        
        $index = new xmldb_index('user_integration_idx', XMLDB_INDEX_NOTUNIQUE, ['user_id', 'integration']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025120102, 'local', 'cdo_ok');
    }

    return true;
}







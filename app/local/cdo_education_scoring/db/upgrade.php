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
 * Upgrade script for local_cdo_education_scoring plugin.
 *
 * @package     local_cdo_education_scoring
 * @category    upgrade
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function for local_cdo_education_scoring plugin.
 *
 * @param int $oldversion The version we are upgrading from.
 * @return bool True on success, false on failure.
 */
function xmldb_local_cdo_education_scoring_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024100100) {
        // Первая версия - таблицы создаются через install.xml.
    }

    if ($oldversion < 2024100103) {
        // Версия 2024100103 - добавлена функция get_active_surveys для студентов.
        // External functions обновляются автоматически через db/services.php.
    }

    if ($oldversion < 2024100104) {
        // Версия 2024100104 - добавлена функция submit_survey_response для студентов.
        // External functions обновляются автоматически через db/services.php.
    }

    // Версия 2024100104 - добавлена возможность заполнения анкет студентами.

    if ($oldversion < 2024100106) {
        // Версия 2024100106 - добавлены поля teacher_id и discipline_id в таблицу response.
        // Определяем, какое имя таблицы используется (старое или новое)
        $oldTableName = 'local_cdo_education_scoring_response';
        $newTableName = 'local_cdo_edu_score_resp';
        
        $oldTable = new \xmldb_table($oldTableName);
        $newTable = new \xmldb_table($newTableName);
        
        // Определяем, какую таблицу использовать
        if ($dbman->table_exists($newTable)) {
            $table = $newTable;
        } elseif ($dbman->table_exists($oldTable)) {
            $table = $oldTable;
        } else {
            // Таблица не существует, пропускаем
            return true;
        }
        
        // Добавляем поле teacher_id
        $field = new \xmldb_field('teacher_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'userid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
        // Добавляем поле discipline_id
        $field = new \xmldb_field('discipline_id', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'teacher_id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
        // Добавляем индекс для быстрого поиска по surveyid, userid и teacher_id
        $index = new \xmldb_index('surveyid_userid_teacherid', XMLDB_INDEX_NOTUNIQUE, ['surveyid', 'userid', 'teacher_id']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
    }

    if ($oldversion < 2024100110) {
        // Версия 2024100110 - переименование таблиц для соответствия лимиту в 28 символов.
        $tablesToRename = [
            'local_cdo_education_scoring_survey' => 'local_cdo_edu_score_survey',
            'local_cdo_education_scoring_question' => 'local_cdo_edu_score_quest',
            'local_cdo_education_scoring_response' => 'local_cdo_edu_score_resp',
        ];
        
        foreach ($tablesToRename as $oldName => $newName) {
            $oldTable = new \xmldb_table($oldName);
            $newTable = new \xmldb_table($newName);
            
            // Если существует старая таблица и новая не существует, переименовываем
            if ($dbman->table_exists($oldTable) && !$dbman->table_exists($newTable)) {
                try {
                    $dbman->rename_table($oldTable, $newName);
                } catch (\Exception $e) {
                    // Логируем ошибку, но продолжаем выполнение
                    debugging("Ошибка при переименовании таблицы {$oldName} в {$newName}: " . $e->getMessage(), DEBUG_NORMAL);
                }
            }
        }
    }

    if ($oldversion < 2024100112) {
        // Версия 2024100112 - добавление полей teacher_id, discipline_id, discipline_name
        $tableName = 'local_cdo_edu_score_resp';
        $table = new \xmldb_table($tableName);
        
        if ($dbman->table_exists($table)) {
            // Добавляем поле teacher_id если не существует
            $field = new \xmldb_field('teacher_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'responsevalue');
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
            
            // Добавляем поле discipline_id если не существует
            $field = new \xmldb_field('discipline_id', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'teacher_id');
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
            
            // Добавляем поле discipline_name если не существует
            $field = new \xmldb_field('discipline_name', XMLDB_TYPE_CHAR, '500', null, null, null, null, 'discipline_id');
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
            
            // Добавляем индекс для teacher_id
            $index = new \xmldb_index('teacher_id', XMLDB_INDEX_NOTUNIQUE, ['teacher_id']);
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
            
            // Добавляем составной индекс
            $index = new \xmldb_index('surveyid_userid_teacher', XMLDB_INDEX_NOTUNIQUE, ['surveyid', 'userid', 'teacher_id']);
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
            
            // Добавляем составной индекс с discipline_id для проверки уникальности по дисциплине
            $index = new \xmldb_index('survey_user_teacher_disc', XMLDB_INDEX_NOTUNIQUE, ['surveyid', 'userid', 'teacher_id', 'discipline_id']);
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }
    }

    if ($oldversion < 2024100115) {
        // Версия 2024100115 - добавление поля description для вопросов
        $tableName = 'local_cdo_edu_score_quest';
        $table = new \xmldb_table($tableName);
        
        if ($dbman->table_exists($table)) {
            // Добавляем поле description если не существует
            $field = new \xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'questiontext');
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
    }

    return true;
}


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
 * Admin settings for local_cdo_education_scoring plugin.
 *
 * @package     local_cdo_education_scoring
 * @category    admin
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * Обработка очистки таблиц плагина.
 */
function local_cdo_education_scoring_handle_clear_tables() {
    global $DB;

    // Проверка прав доступа
    //require_capability('moodle/site:config', context_system::instance());

    $confirm = optional_param('confirm', 0, PARAM_INT);
    $sesskey = optional_param('sesskey', '', PARAM_TEXT);

    if ($confirm && confirm_sesskey($sesskey)) {
        // Список таблиц плагина в порядке удаления (сначала дочерние, потом родительские)
        $tables = [
            'local_cdo_edu_score_resp',  // Сначала ответы (зависят от вопросов и анкет)
            'local_cdo_edu_score_quest',  // Потом вопросы (зависят от анкет)
            'local_cdo_edu_score_survey',    // В конце анкеты (родительская таблица)
        ];

        // Отключаем проверку внешних ключей для MySQL
        $dbtype = $DB->get_dbfamily();
        if ($dbtype === 'mysql') {
            $DB->execute("SET FOREIGN_KEY_CHECKS = 0");
        }

        // Выполняем TRUNCATE для каждой таблицы
        foreach ($tables as $table) {
            try {
                // Пытаемся использовать TRUNCATE
                $DB->execute("TRUNCATE TABLE {" . $table . "}");
            } catch (\Exception $e) {
                // Если TRUNCATE не поддерживается, используем DELETE
                $DB->delete_records($table);
            }
        }

        // Включаем обратно проверку внешних ключей для MySQL
        if ($dbtype === 'mysql') {
            $DB->execute("SET FOREIGN_KEY_CHECKS = 1");
        }

        // Очищаем кэш
        \cache_helper::purge_all();

        redirect(
            new \moodle_url('/admin/settings.php', ['section' => 'local_cdo_education_scoring_settings']),
            get_string('tables_cleared', 'local_cdo_education_scoring'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}


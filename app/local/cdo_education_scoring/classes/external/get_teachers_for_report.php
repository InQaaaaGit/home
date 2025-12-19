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
 * External API для получения списка преподавателей на основе сданных анкет.
 *
 * @package     local_cdo_education_scoring
 * @category    external
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cdo_education_scoring\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once(__DIR__ . '/../../lib.php');

/**
 * External API для получения списка преподавателей на основе сданных анкет.
 */
class get_teachers_for_report extends \external_api {

    /**
     * Описание параметров функции get_teachers_for_report.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'surveyid' => new \external_value(PARAM_INT, 'ID анкеты', VALUE_REQUIRED),
        ]);
    }

    /**
     * Получение списка преподавателей на основе сданных анкет.
     *
     * @param int $surveyid ID анкеты
     * @return array Список преподавателей с данными
     */
    public static function execute(int $surveyid): array {
        global $DB;

        // Проверка прав доступа.
        $context = \context_system::instance();
        require_capability('local/cdo_education_scoring:manage', $context);

        // Валидация параметров.
        $params = self::validate_parameters(self::execute_parameters(), [
            'surveyid' => $surveyid,
        ]);

        // Получение актуального имени таблицы.
        $responseTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_resp',
            'local_cdo_education_scoring_response'
        );
        
        // Получаем список преподавателей, для которых есть сданные анкеты
        $sql = "
            SELECT DISTINCT
                r.teacher_id,
                u.id,
                u.firstname,
                u.lastname
            FROM {" . $responseTable . "} r
            INNER JOIN {user} u ON u.id = r.teacher_id
            WHERE r.surveyid = :surveyid
            AND r.teacher_id IS NOT NULL
            ORDER BY u.lastname, u.firstname
        ";

        $teachers = $DB->get_records_sql($sql, ['surveyid' => $params['surveyid']]);

        // Формируем результат
        $result = [];
        foreach ($teachers as $teacher) {
            $result[] = [
                'id' => (int)$teacher->id,
                'fullname' => \local_cdo_education_scoring_format_fullname($teacher),
            ];
        }

        return $result;
    }

    /**
     * Описание возвращаемых данных функции get_teachers_for_report.
     *
     * @return \external_multiple_structure
     */
    public static function execute_returns(): \external_multiple_structure {
        return new \external_multiple_structure(
            new \external_single_structure([
                'id' => new \external_value(PARAM_INT, 'ID преподавателя'),
                'fullname' => new \external_value(PARAM_TEXT, 'Полное имя преподавателя'),
            ]),
            'Список преподавателей на основе сданных анкет'
        );
    }
}


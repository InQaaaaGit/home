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
 * External API для получения списка преподавателей по дисциплине.
 *
 * @package     local_cdo_education_scoring
 * @category    external
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cdo_education_scoring\external;

use tool_cdo_config\di;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once(__DIR__ . '/../../lib.php');

/**
 * External API для получения списка преподавателей по дисциплине.
 */
class get_teachers extends \external_api {

    /**
     * Описание параметров функции get_teachers.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'discipline_id' => new \external_value(PARAM_TEXT, 'Код дисциплины', VALUE_REQUIRED),
            'surveyid' => new \external_value(PARAM_INT, 'ID анкеты для фильтрации заполненных', VALUE_OPTIONAL, null),
        ]);
    }

    /**
     * Получение списка преподавателей по дисциплине.
     *
     * @param string $discipline_id Код дисциплины
     * @param int|null $surveyid ID анкеты для исключения уже заполненных преподавателей
     * @return array Результат с преподавателями и статистикой
     */
    public static function execute(string $discipline_id, ?int $surveyid = null): array {
        global $DB, $USER;

        // Проверка прав доступа.
        $context = \context_system::instance();
        require_capability('local/cdo_education_scoring:view', $context);

        // Валидация параметров.
        $params = self::validate_parameters(self::execute_parameters(), [
            'discipline_id' => $discipline_id,
            'surveyid' => $surveyid,
        ]);

        $options = di::get_instance()->get_request_options();
        $options->set_properties([
            'discipline_id' => $params['discipline_id'],
            'user_id' => $USER->id,
        ]);

        $allTeachers = di::get_instance()
            ->get_request('get_teachers_for_scoring')
            ->request($options)
            ->get_request_result()
            ->to_array();

        $totalCount = count($allTeachers);
        $completedCount = 0;
        $availableTeachers = $allTeachers;

        // Если передан surveyid, исключаем преподавателей, для которых пользователь уже заполнил анкету по данной дисциплине
        if (!empty($params['surveyid']) && !empty($allTeachers)) {
            $responseTable = \local_cdo_education_scoring_get_table_name(
                'local_cdo_edu_score_resp',
                'local_cdo_education_scoring_response'
            );

            // Получаем список teacher_id, для которых пользователь уже заполнил анкету по данной дисциплине
            $sql = "SELECT DISTINCT teacher_id 
                    FROM {" . $responseTable . "} 
                    WHERE surveyid = :surveyid 
                      AND userid = :userid 
                      AND discipline_id = :discipline_id
                      AND teacher_id IS NOT NULL";
            
            $completedTeachers = $DB->get_records_sql($sql, [
                'surveyid' => $params['surveyid'],
                'userid' => $USER->id,
                'discipline_id' => $params['discipline_id'],
            ]);

            if (!empty($completedTeachers)) {
                $completedTeacherIds = array_keys($completedTeachers);
                $completedCount = count($completedTeacherIds);
                
                // Фильтруем преподавателей, исключая уже заполненных
                $availableTeachers = array_filter($allTeachers, function($teacher) use ($completedTeacherIds) {
                    return !in_array($teacher['id'], $completedTeacherIds);
                });
                
                // Переиндексируем массив
                $availableTeachers = array_values($availableTeachers);
            }
        }

        return [
            'teachers' => $availableTeachers,
            'total_count' => $totalCount,
            'completed_count' => $completedCount,
            'all_completed' => ($totalCount > 0 && count($availableTeachers) === 0),
        ];
    }

    /**
     * Описание возвращаемых данных функции get_teachers.
     *
     * @return \external_single_structure
     */
    public static function execute_returns(): \external_single_structure {
        return new \external_single_structure([
            'teachers' => new \external_multiple_structure(
                new \external_single_structure([
                    'id' => new \external_value(PARAM_INT, 'ID преподавателя'),
                    'fullname' => new \external_value(PARAM_TEXT, 'Полное имя преподавателя'),
                    'email' => new \external_value(PARAM_TEXT, 'Email преподавателя', VALUE_OPTIONAL),
                ]),
                'Список доступных преподавателей'
            ),
            'total_count' => new \external_value(PARAM_INT, 'Общее количество преподавателей'),
            'completed_count' => new \external_value(PARAM_INT, 'Количество уже заполненных'),
            'all_completed' => new \external_value(PARAM_BOOL, 'Все преподаватели заполнены'),
        ], 'Результат с преподавателями и статистикой');
    }
}


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
 * External API для получения статистики сданных анкет по преподавателю.
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
 * External API для получения статистики сданных анкет по преподавателю.
 */
class get_survey_stats extends \external_api {

    /**
     * Описание параметров функции get_survey_stats.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'teacher_id' => new \external_value(PARAM_INT, 'ID преподавателя', VALUE_REQUIRED),
        ]);
    }

    /**
     * Получение статистики сданных анкет по преподавателю.
     *
     * @param int $teacher_id ID преподавателя
     * @return array Статистика сданных анкет
     */
    public static function execute(int $teacher_id): array {
        global $DB;

        // Проверка прав доступа (преподаватель или администратор).
        $context = \context_system::instance();
        require_capability('local/cdo_education_scoring:viewstats', $context);

        // Валидация параметров.
        $params = self::validate_parameters(self::execute_parameters(), [
            'teacher_id' => $teacher_id,
        ]);

        // Получение актуальных имен таблиц
        $surveyTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_survey',
            'local_cdo_education_scoring_survey'
        );
        $responseTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_resp',
            'local_cdo_education_scoring_response'
        );

        // Получаем информацию о преподавателе
        $teacher = $DB->get_record('user', ['id' => $params['teacher_id']]);
        $teacherName = $teacher ? \local_cdo_education_scoring_format_fullname($teacher) : '';

        // Получаем все анкеты, по которым есть ответы для данного преподавателя
        $sql = "
            SELECT 
                s.id AS surveyid,
                s.title AS survey_title,
                r.discipline_id,
                r.discipline_name,
                COUNT(DISTINCT r.userid) AS completed_count
            FROM {" . $responseTable . "} r
            INNER JOIN {" . $surveyTable . "} s ON s.id = r.surveyid
            WHERE r.teacher_id = :teacher_id
            GROUP BY s.id, s.title, r.discipline_id, r.discipline_name
            ORDER BY s.title, r.discipline_name
        ";

        $results = $DB->get_records_sql($sql, [
            'teacher_id' => $params['teacher_id'],
        ]);

        // Формируем результат
        $surveys = [];
        foreach ($results as $row) {
            $surveys[] = [
                'surveyid' => (int)$row->surveyid,
                'survey_title' => $row->survey_title ?? '',
                'discipline_id' => $row->discipline_id ?? '',
                'discipline_name' => $row->discipline_name ?? '',
                'completed_count' => (int)$row->completed_count,
            ];
        }

        return [
            'teacher_id' => $params['teacher_id'],
            'teacher_name' => $teacherName,
            'total_surveys' => count($surveys),
            'surveys' => $surveys,
        ];
    }

    /**
     * Описание возвращаемых данных функции get_survey_stats.
     *
     * @return \external_single_structure
     */
    public static function execute_returns(): \external_single_structure {
        return new \external_single_structure([
            'teacher_id' => new \external_value(PARAM_INT, 'ID преподавателя'),
            'teacher_name' => new \external_value(PARAM_TEXT, 'ФИО преподавателя'),
            'total_surveys' => new \external_value(PARAM_INT, 'Общее количество записей'),
            'surveys' => new \external_multiple_structure(
                new \external_single_structure([
                    'surveyid' => new \external_value(PARAM_INT, 'ID анкеты'),
                    'survey_title' => new \external_value(PARAM_TEXT, 'Название анкеты'),
                    'discipline_id' => new \external_value(PARAM_TEXT, 'Код дисциплины'),
                    'discipline_name' => new \external_value(PARAM_TEXT, 'Название дисциплины'),
                    'completed_count' => new \external_value(PARAM_INT, 'Количество сданных анкет'),
                ]),
                'Список анкет со статистикой'
            ),
        ], 'Статистика сданных анкет по преподавателю');
    }
}

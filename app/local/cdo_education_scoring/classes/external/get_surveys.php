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
 * External API для получения списка анкет.
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
 * External API для получения списка анкет.
 */
class get_surveys extends \external_api {

    /**
     * Описание параметров функции get_surveys.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([]);
    }

    /**
     * Получение списка анкет.
     *
     * @return array Описание возвращаемых данных
     */
    public static function execute(): array {
        global $DB, $USER;

        // Проверка прав доступа.
        $context = \context_system::instance();
        require_capability('local/cdo_education_scoring:manage', $context);

        // Получение актуальных имен таблиц.
        $surveyTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_survey',
            'local_cdo_education_scoring_survey'
        );
        $questionTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_quest',
            'local_cdo_education_scoring_question'
        );

        // Получение анкет.
        $surveys = $DB->get_records($surveyTable, null, 'timecreated DESC');

        $result = [];
        foreach ($surveys as $survey) {
            // Получение вопросов для каждой анкеты.
            $questions = $DB->get_records(
                $questionTable,
                ['surveyid' => $survey->id],
                'sortorder ASC'
            );

            $questionsdata = [];
            foreach ($questions as $question) {
                $questionsdata[] = [
                    'id' => $question->id,
                    'text' => $question->questiontext,
                    'description' => $question->description ?? '',
                    'type' => $question->questiontype,
                    'sortorder' => $question->sortorder,
                ];
            }

            $result[] = [
                'id' => $survey->id,
                'title' => $survey->title,
                'description' => $survey->description ?? '',
                'durationDays' => $survey->durationdays,
                'isActive' => (bool)$survey->isactive,
                'questions' => $questionsdata,
                'createdAt' => date('c', $survey->timecreated),
            ];
        }

        return $result;
    }

    /**
     * Описание возвращаемых данных функции get_surveys.
     *
     * @return \external_multiple_structure
     */
    public static function execute_returns(): \external_multiple_structure {
        return new \external_multiple_structure(
            new \external_single_structure([
                'id' => new \external_value(PARAM_INT, 'ID анкеты'),
                'title' => new \external_value(PARAM_TEXT, 'Название анкеты'),
                'description' => new \external_value(PARAM_TEXT, 'Описание анкеты', VALUE_OPTIONAL),
                'durationDays' => new \external_value(PARAM_INT, 'Срок проведения в днях'),
                'isActive' => new \external_value(PARAM_BOOL, 'Статус активности'),
                'questions' => new \external_multiple_structure(
                    new \external_single_structure([
                        'id' => new \external_value(PARAM_INT, 'ID вопроса'),
                        'text' => new \external_value(PARAM_TEXT, 'Текст вопроса'),
                        'description' => new \external_value(PARAM_TEXT, 'Описание вопроса (подсказка)', VALUE_OPTIONAL),
                        'type' => new \external_value(PARAM_TEXT, 'Тип вопроса'),
                        'sortorder' => new \external_value(PARAM_INT, 'Порядок сортировки'),
                    ]),
                    'Список вопросов'
                ),
                'createdAt' => new \external_value(PARAM_TEXT, 'Дата создания'),
            ])
        );
    }
}


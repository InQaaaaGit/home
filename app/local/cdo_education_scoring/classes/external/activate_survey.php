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
 * External API для активации/деактивации анкеты.
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
 * External API для активации/деактивации анкеты.
 */
class activate_survey extends \external_api {

    /**
     * Описание параметров функции activate_survey.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'surveyid' => new \external_value(PARAM_INT, 'ID анкеты', VALUE_REQUIRED),
            'isactive' => new \external_value(PARAM_BOOL, 'Статус активности', VALUE_REQUIRED),
        ]);
    }

    /**
     * Активация/деактивация анкеты.
     *
     * @param int $surveyid ID анкеты
     * @param bool $isactive Статус активности
     * @return array Данные анкеты
     */
    public static function execute(int $surveyid, bool $isactive): array {
        global $DB, $USER;

        // Проверка прав доступа.
        $context = \context_system::instance();
        require_capability('local/cdo_education_scoring:manage', $context);

        // Валидация параметров.
        $params = self::validate_parameters(self::execute_parameters(), [
            'surveyid' => $surveyid,
            'isactive' => $isactive,
        ]);

        // Получение актуальных имен таблиц.
        $surveyTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_survey',
            'local_cdo_education_scoring_survey'
        );
        $questionTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_quest',
            'local_cdo_education_scoring_question'
        );

        // Проверка существования анкеты.
        $survey = $DB->get_record($surveyTable, ['id' => $params['surveyid']]);
        if (!$survey) {
            throw new \invalid_parameter_exception('Анкета не найдена');
        }

        // Обновление статуса.
        $survey->isactive = $params['isactive'] ? 1 : 0;
        $survey->usermodified = $USER->id;
        $survey->timemodified = time();

        $DB->update_record($surveyTable, $survey);

        // Получение вопросов.
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

        return [
            'id' => $survey->id,
            'title' => $survey->title,
            'description' => $survey->description ?? '',
            'durationDays' => $survey->durationdays,
            'isActive' => (bool)$survey->isactive,
            'questions' => $questionsdata,
            'createdAt' => date('c', $survey->timecreated),
        ];
    }

    /**
     * Описание возвращаемых данных функции activate_survey.
     *
     * @return \external_single_structure
     */
    public static function execute_returns(): \external_single_structure {
        return new \external_single_structure([
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
        ]);
    }
}


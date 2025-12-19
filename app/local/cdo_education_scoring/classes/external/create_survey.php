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
 * External API для создания анкеты.
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
 * External API для создания анкеты.
 */
class create_survey extends \external_api {

    /**
     * Описание параметров функции create_survey.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'title' => new \external_value(PARAM_TEXT, 'Название анкеты', VALUE_REQUIRED),
            'description' => new \external_value(PARAM_TEXT, 'Описание анкеты', VALUE_DEFAULT, ''),
            'durationdays' => new \external_value(PARAM_INT, 'Срок проведения в днях', VALUE_REQUIRED),
            'questions' => new \external_multiple_structure(
                new \external_single_structure([
                    'text' => new \external_value(PARAM_TEXT, 'Текст вопроса', VALUE_REQUIRED),
                    'description' => new \external_value(PARAM_TEXT, 'Описание вопроса (подсказка)', VALUE_DEFAULT, ''),
                    'type' => new \external_value(PARAM_TEXT, 'Тип вопроса (scale или text)', VALUE_REQUIRED),
                    'id' => new \external_value(PARAM_INT, 'ID вопроса (игнорируется при создании)', VALUE_OPTIONAL),
                    'sortorder' => new \external_value(PARAM_INT, 'Порядок сортировки (игнорируется при создании)', VALUE_OPTIONAL),
                ]),
                'Список вопросов',
                VALUE_REQUIRED
            ),
        ]);
    }

    /**
     * Создание анкеты.
     *
     * @param string $title Название анкеты
     * @param string $description Описание анкеты
     * @param int $durationdays Срок проведения в днях
     * @param array $questions Список вопросов
     * @return array Данные созданной анкеты
     */
    public static function execute(string $title, string $description, int $durationdays, array $questions): array {
        global $DB, $USER;

        // Проверка прав доступа.
        $context = \context_system::instance();
        require_capability('local/cdo_education_scoring:manage', $context);

        // Валидация параметров.
        $params = self::validate_parameters(self::execute_parameters(), [
            'title' => $title,
            'description' => $description,
            'durationdays' => $durationdays,
            'questions' => $questions,
        ]);

        // Валидация данных.
        if (empty(trim($params['title']))) {
            throw new \invalid_parameter_exception('Название анкеты не может быть пустым');
        }

        if ($params['durationdays'] < 1) {
            throw new \invalid_parameter_exception('Срок проведения должен быть больше 0');
        }

        if (empty($params['questions'])) {
            throw new \invalid_parameter_exception('Анкета должна содержать хотя бы один вопрос');
        }

        // Валидация вопросов.
        $validtypes = ['scale', 'text'];
        foreach ($params['questions'] as $question) {
            if (empty(trim($question['text']))) {
                throw new \invalid_parameter_exception('Текст вопроса не может быть пустым');
            }
            if (!in_array($question['type'], $validtypes)) {
                throw new \invalid_parameter_exception('Неверный тип вопроса');
            }
        }

        // Получение актуальных имен таблиц.
        $surveyTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_survey',
            'local_cdo_education_scoring_survey'
        );
        $questionTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_quest',
            'local_cdo_education_scoring_question'
        );

        // Начало транзакции.
        $transaction = $DB->start_delegated_transaction();

        try {
            // Создание анкеты.
            $survey = new \stdClass();
            $survey->title = trim($params['title']);
            $survey->description = trim($params['description']);
            $survey->durationdays = $params['durationdays'];
            $survey->isactive = 0;
            $survey->usercreated = $USER->id;
            $survey->timecreated = time();
            $survey->timemodified = time();

            $surveyid = $DB->insert_record($surveyTable, $survey);

            // Создание вопросов.
            $sortorder = 0;
            foreach ($params['questions'] as $questiondata) {
                $question = new \stdClass();
                $question->surveyid = $surveyid;
                $question->questiontext = trim($questiondata['text']);
                $question->description = !empty($questiondata['description']) ? trim($questiondata['description']) : null;
                $question->questiontype = $questiondata['type'];
                $question->sortorder = $sortorder++;
                $question->timecreated = time();
                $question->timemodified = time();

                $DB->insert_record($questionTable, $question);
            }

            // Подтверждение транзакции.
            $transaction->allow_commit();

            // Возврат данных созданной анкеты.
            $createdsurvey = $DB->get_record($surveyTable, ['id' => $surveyid]);
            $createdquestions = $DB->get_records(
                $questionTable,
                ['surveyid' => $surveyid],
                'sortorder ASC'
            );

            $questionsdata = [];
            foreach ($createdquestions as $question) {
                $questionsdata[] = [
                    'id' => $question->id,
                    'text' => $question->questiontext,
                    'description' => $question->description ?? '',
                    'type' => $question->questiontype,
                    'sortorder' => $question->sortorder,
                ];
            }

            return [
                'id' => $createdsurvey->id,
                'title' => $createdsurvey->title,
                'description' => $createdsurvey->description ?? '',
                'durationDays' => $createdsurvey->durationdays,
                'isActive' => (bool)$createdsurvey->isactive,
                'questions' => $questionsdata,
                'createdAt' => date('c', $createdsurvey->timecreated),
            ];
        } catch (\Exception $e) {
            $transaction->rollback($e);
            throw $e;
        }
    }

    /**
     * Описание возвращаемых данных функции create_survey.
     *
     * @return \external_single_structure
     */
    public static function execute_returns(): \external_single_structure {
        return new \external_single_structure([
            'id' => new \external_value(PARAM_INT, 'ID созданной анкеты'),
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


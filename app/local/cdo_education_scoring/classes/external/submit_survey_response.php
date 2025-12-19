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
 * External API для отправки ответов на анкету.
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
 * External API для отправки ответов на анкету.
 */
class submit_survey_response extends \external_api {

    /**
     * Описание параметров функции submit_survey_response.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'surveyid' => new \external_value(PARAM_INT, 'ID анкеты', VALUE_REQUIRED),
            'answers' => new \external_multiple_structure(
                new \external_single_structure([
                    'questionid' => new \external_value(PARAM_INT, 'ID вопроса', VALUE_REQUIRED),
                    'value' => new \external_value(PARAM_RAW, 'Значение ответа', VALUE_REQUIRED),
                ]),
                'Массив ответов на вопросы',
                VALUE_REQUIRED
            ),
            'discipline_id' => new \external_value(PARAM_TEXT, 'Код дисциплины', VALUE_OPTIONAL),
            'discipline_name' => new \external_value(PARAM_TEXT, 'Название дисциплины', VALUE_OPTIONAL),
            'teacher_id' => new \external_value(PARAM_INT, 'ID преподавателя', VALUE_OPTIONAL),
        ]);
    }

    /**
     * Отправка ответов на анкету.
     *
     * @param int $surveyid ID анкеты
     * @param array $answers Массив ответов
     * @param string|null $discipline_id Код дисциплины
     * @param string|null $discipline_name Название дисциплины
     * @param int|null $teacher_id ID преподавателя
     * @return array Результат сохранения
     */
    public static function execute(
        int $surveyid,
        array $answers,
        ?string $discipline_id = null,
        ?string $discipline_name = null,
        ?int $teacher_id = null
    ): array {
        global $DB, $USER;

        // Проверка прав доступа.
        $context = \context_system::instance();
        require_capability('local/cdo_education_scoring:view', $context);

        // Валидация параметров.
        $params = self::validate_parameters(self::execute_parameters(), [
            'surveyid' => $surveyid,
            'answers' => $answers,
            'discipline_id' => $discipline_id,
            'discipline_name' => $discipline_name,
            'teacher_id' => $teacher_id,
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
        $responseTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_resp',
            'local_cdo_education_scoring_response'
        );

        // Получаем анкету для дальнейшей обработки
        $survey = $DB->get_record($surveyTable, ['id' => $params['surveyid']]);
        if (!$survey) {
            throw new \invalid_parameter_exception('Анкета не найдена');
        }

        // Получаем параметры для проверки дублирования
        $teacherId = !empty($params['teacher_id']) ? (int)$params['teacher_id'] : null;
        $disciplineId = !empty($params['discipline_id']) ? trim($params['discipline_id']) : null;

        // Проверка на дублирование: пользователь уже заполнял эту анкету для данного преподавателя по данной дисциплине
        if ($teacherId && $disciplineId) {
            // Проверяем комбинацию: анкета + пользователь + преподаватель + дисциплина
            $sql = "SELECT id FROM {" . $responseTable . "} 
                    WHERE surveyid = :surveyid 
                      AND userid = :userid 
                      AND teacher_id = :teacher_id 
                      AND discipline_id = :discipline_id";
            $existingResponse = $DB->get_record_sql($sql, [
                'surveyid' => $params['surveyid'],
                'userid' => $USER->id,
                'teacher_id' => $teacherId,
                'discipline_id' => $disciplineId,
            ]);
            if ($existingResponse) {
                throw new \invalid_parameter_exception('Вы уже заполнили эту анкету для данного преподавателя по данной дисциплине');
            }
        } elseif ($teacherId) {
            // Если только преподаватель указан (без дисциплины)
            $sql = "SELECT id FROM {" . $responseTable . "} 
                    WHERE surveyid = :surveyid 
                      AND userid = :userid 
                      AND teacher_id = :teacher_id 
                      AND discipline_id IS NULL";
            $existingResponse = $DB->get_record_sql($sql, [
                'surveyid' => $params['surveyid'],
                'userid' => $USER->id,
                'teacher_id' => $teacherId,
            ]);
            if ($existingResponse) {
                throw new \invalid_parameter_exception('Вы уже заполнили эту анкету для данного преподавателя');
            }
        } else {
            // Если ни преподаватель, ни дисциплина не указаны
            $sql = "SELECT id FROM {" . $responseTable . "} 
                    WHERE surveyid = :surveyid 
                      AND userid = :userid 
                      AND teacher_id IS NULL 
                      AND discipline_id IS NULL";
            $existingResponse = $DB->get_record_sql($sql, [
                'surveyid' => $params['surveyid'],
                'userid' => $USER->id,
            ]);
            if ($existingResponse) {
                throw new \invalid_parameter_exception('Вы уже заполнили эту анкету');
            }
        }

        // Получение всех вопросов анкеты.
        $questions = $DB->get_records(
            $questionTable,
            ['surveyid' => $survey->id],
            'sortorder ASC'
        );

        if (empty($questions)) {
            throw new \invalid_parameter_exception('Анкета не содержит вопросов');
        }

        // Валидация ответов - проверяем, что ответили на все вопросы.
        $questionIds = array_map(function($q) {
            return $q->id;
        }, $questions);

        $answerQuestionIds = array_map(function($a) {
            return $a['questionid'];
        }, $params['answers']);

        $missingQuestions = array_diff($questionIds, $answerQuestionIds);
        if (!empty($missingQuestions)) {
            throw new \invalid_parameter_exception('Необходимо ответить на все вопросы');
        }

        // Валидация типов ответов.
        foreach ($params['answers'] as $answer) {
            $question = $DB->get_record($questionTable, ['id' => $answer['questionid']]);
            if (!$question || $question->surveyid != $survey->id) {
                throw new \invalid_parameter_exception('Неверный ID вопроса');
            }

            // Валидация балльной шкалы.
            if ($question->questiontype === 'scale') {
                $value = (int)$answer['value'];
                if ($value < 1 || $value > 5) {
                    throw new \invalid_parameter_exception('Оценка должна быть от 1 до 5');
                }
            }

            // Валидация текстового ответа.
            if ($question->questiontype === 'text') {
                $value = trim($answer['value']);
                if (empty($value)) {
                    throw new \invalid_parameter_exception('Текстовый ответ не может быть пустым');
                }
            }
        }

        // Начало транзакции.
        $transaction = $DB->start_delegated_transaction();

        try {
            $timecreated = time();
            $disciplineId = !empty($params['discipline_id']) ? trim($params['discipline_id']) : null;
            $disciplineName = !empty($params['discipline_name']) ? trim($params['discipline_name']) : null;
            $teacherId = !empty($params['teacher_id']) ? (int)$params['teacher_id'] : null;

            // Сохранение всех ответов.
            foreach ($params['answers'] as $answer) {
                $response = new \stdClass();
                $response->surveyid = $survey->id;
                $response->questionid = $answer['questionid'];
                $response->userid = $USER->id;
                $response->responsevalue = (string)$answer['value'];
                $response->timecreated = $timecreated;
                $response->teacher_id = $teacherId;
                $response->discipline_id = $disciplineId;
                $response->discipline_name = $disciplineName;

                $DB->insert_record($responseTable, $response);
            }

            // Подтверждение транзакции.
            $transaction->allow_commit();

            return [
                'success' => true,
                'message' => 'Ответы успешно сохранены',
                'surveyid' => $survey->id,
            ];
        } catch (\Exception $e) {
            $transaction->rollback($e);
            throw $e;
        }
    }

    /**
     * Описание возвращаемых данных функции submit_survey_response.
     *
     * @return \external_single_structure
     */
    public static function execute_returns(): \external_single_structure {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Успешность сохранения'),
            'message' => new \external_value(PARAM_TEXT, 'Сообщение'),
            'surveyid' => new \external_value(PARAM_INT, 'ID анкеты'),
        ]);
    }
}


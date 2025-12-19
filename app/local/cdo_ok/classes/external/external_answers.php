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
 * External API для работы с ответами на вопросы.
 *
 * @package    local_cdo_ok
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cdo_ok\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_cdo_ok\controllers\answers_controller;
use local_cdo_ok\external\helper;

/**
 * External API для управления ответами на вопросы анкет.
 */
class external_answers extends external_api {

    /**
     * Описание параметров для create_answer.
     *
     * @return external_function_parameters
     */
    public static function create_answer_parameters(): external_function_parameters {
        global $USER;
        return new external_function_parameters([
            'data' => new external_single_structure([
                'user_id' => new external_value(PARAM_INT, 'ID пользователя', VALUE_DEFAULT, $USER->id),
                'answer' => new external_value(PARAM_TEXT, 'Текст ответа', VALUE_REQUIRED),
                'question_id' => new external_value(PARAM_INT, 'ID вопроса', VALUE_REQUIRED),
                'integration' => new external_value(PARAM_TEXT, 'Тип интеграции', VALUE_REQUIRED),
                'discipline' => new external_value(PARAM_TEXT, 'Дисциплина', VALUE_OPTIONAL),
            ], 'Данные ответа'),
        ]);
    }

    /**
     * Создание ответа на вопрос.
     *
     * @param array $data Данные ответа
     * @return void
     */
    public static function create_answer(array $data): void {
        $params = self::validate_parameters(self::create_answer_parameters(), ['data' => $data]);
        
        $controller = new answers_controller();
        $controller->create_update($params['data']);
    }

    /**
     * Описание возвращаемого значения для create_answer.
     *
     * @return null
     */
    public static function create_answer_returns(): ?external_value {
        return null;
    }

    /**
     * Описание параметров для get_question_with_answers.
     *
     * @return external_function_parameters
     */
    public static function get_question_with_answers_parameters(): external_function_parameters {
        return new external_function_parameters([
            'params' => new external_single_structure([
                'group_tab' => new external_value(PARAM_INT, 'Номер группы', VALUE_OPTIONAL),
                'visible' => new external_value(PARAM_INT, 'Видимость вопроса', VALUE_OPTIONAL),
                'integration' => new external_value(PARAM_TEXT, 'Тип интеграции', VALUE_REQUIRED),
            ], 'Параметры для получения вопросов с ответами', VALUE_DEFAULT, []),
        ]);
    }

    /**
     * Получение вопросов с ответами.
     *
     * @param array $params Параметры запроса
     * @return array Массив вопросов с ответами
     */
    public static function get_question_with_answers(array $params = []): array {
        $params = self::validate_parameters(self::get_question_with_answers_parameters(), ['params' => $params]);
        
        $controller = new answers_controller();
        return $controller->get_question_with_answers($params['params']);
    }

    /**
     * Описание возвращаемого значения для get_question_with_answers.
     *
     * @return external_multiple_structure
     */
    public static function get_question_with_answers_returns(): external_multiple_structure {
        return new external_multiple_structure(
            helper::get_external_question_object([
                'answer' => new external_value(PARAM_TEXT, 'Текст ответа', VALUE_REQUIRED),
            ]),
            'Массив вопросов с ответами'
        );
    }
}
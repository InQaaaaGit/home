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
 * External API для работы с вопросами анкет.
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
use local_cdo_ok\controllers\questions_controller;
use stdClass;

/**
 * External API для управления вопросами анкет.
 */
class external_questions extends external_api {

    /**
     * Описание параметров для get_questions.
     *
     * @return external_function_parameters
     */
    public static function get_questions_parameters(): external_function_parameters {
        return new external_function_parameters([
            'params' => new external_single_structure([
                'group_tab' => new external_value(PARAM_INT, 'Номер группы', VALUE_OPTIONAL),
                'visible' => new external_value(PARAM_INT, 'Видимость вопроса', VALUE_OPTIONAL),
            ], 'Параметры для получения вопросов', VALUE_DEFAULT, []),
        ]);
    }

    /**
     * Получение списка вопросов с ответами.
     *
     * @param array $params Параметры запроса
     * @return array Массив вопросов с ответами
     */
    public static function get_questions(array $params = []): array {
        $params = self::validate_parameters(self::get_questions_parameters(), ['params' => $params]);
        
        $controller = new questions_controller();
        return $controller->get_with_answer($params['params']);
    }

    /**
     * Описание возвращаемого значения для get_questions.
     *
     * @return external_multiple_structure
     */
    public static function get_questions_returns(): external_multiple_structure {
        return new external_multiple_structure(
            helper::get_external_question_object([
                'answer' => new external_value(PARAM_TEXT, 'Текст ответа', VALUE_DEFAULT, ''),
                'editStatus' => new external_value(PARAM_BOOL, 'Статус редактирования', VALUE_DEFAULT, false),
            ]),
            'Массив вопросов с ответами'
        );
    }

    /**
     * Описание параметров для create_question.
     *
     * @return external_function_parameters
     */
    public static function create_question_parameters(): external_function_parameters {
        return new external_function_parameters([
            'groupTab' => new external_value(PARAM_INT, 'Выбранная вкладка', VALUE_REQUIRED),
            'sort' => new external_value(PARAM_INT, 'Значение сортировки', VALUE_REQUIRED),
        ]);
    }

    /**
     * Создание нового вопроса.
     *
     * @param int $groupTab Номер вкладки группы
     * @param int $sort Порядок сортировки
     * @return stdClass Созданный вопрос
     */
    public static function create_question(int $groupTab, int $sort): stdClass {
        $params = self::validate_parameters(
            self::create_question_parameters(),
            ['groupTab' => $groupTab, 'sort' => $sort]
        );
        
        $controller = new questions_controller();
        return $controller->create($params['groupTab'], $params['sort']);
    }

    /**
     * Описание возвращаемого значения для create_question.
     *
     * @return null
     */
    public static function create_question_returns(): ?external_value {
        return null;
    }

    /**
     * Описание параметров для update_question.
     *
     * @return external_function_parameters
     */
    public static function update_question_parameters(): external_function_parameters {
        return new external_function_parameters([
            'data' => helper::get_external_question_object([
                'answer' => new external_value(PARAM_TEXT, 'Текст ответа', VALUE_DEFAULT, ''),
                'editStatus' => new external_value(PARAM_BOOL, 'Статус редактирования', VALUE_DEFAULT, false),
            ]),
        ]);
    }

    /**
     * Обновление вопроса.
     *
     * @param array $data Данные вопроса
     * @return bool Результат операции
     */
    public static function update_question(array $data): bool {
        $params = self::validate_parameters(self::update_question_parameters(), ['data' => $data]);
        
        $controller = new questions_controller();
        return $controller->update($params['data']);
    }

    /**
     * Описание возвращаемого значения для update_question.
     *
     * @return null
     */
    public static function update_question_returns(): ?external_value {
        return null;
    }

    /**
     * Описание параметров для delete_question.
     *
     * @return external_function_parameters
     */
    public static function delete_question_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'ID вопроса для удаления', VALUE_REQUIRED),
        ]);
    }

    /**
     * Удаление вопроса.
     *
     * @param int $id ID вопроса
     * @return bool Результат операции
     */
    public static function delete_question(int $id): bool {
        $params = self::validate_parameters(self::delete_question_parameters(), ['id' => $id]);
        
        $controller = new questions_controller();
        return $controller->delete($params['id']);
    }

    /**
     * Описание возвращаемого значения для delete_question.
     *
     * @return null
     */
    public static function delete_question_returns(): ?external_value {
        return null;
    }

    /**
     * Описание параметров для update_questions.
     *
     * @return external_function_parameters
     */
    public static function update_questions_parameters(): external_function_parameters {
        return new external_function_parameters([
            'data' => new external_multiple_structure(
                helper::get_external_question_object([
                    'answer' => new external_value(PARAM_TEXT, 'Текст ответа', VALUE_DEFAULT, ''),
                ]),
                'Массив вопросов для обновления'
            ),
        ]);
    }

    /**
     * Массовое обновление вопросов.
     *
     * @param array $data Массив вопросов
     * @return bool Результат операции
     */
    public static function update_questions(array $data): bool {
        $params = self::validate_parameters(self::update_questions_parameters(), ['data' => $data]);
        
        $controller = new questions_controller();
        foreach ($params['data'] as $questionRecord) {
            $controller->update($questionRecord);
        }
        
        return true;
    }

    /**
     * Описание возвращаемого значения для update_questions.
     *
     * @return null
     */
    public static function update_questions_returns(): ?external_value {
        return null;
    }
}
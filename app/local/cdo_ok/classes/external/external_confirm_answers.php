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
 * External API для работы с подтверждением ответов.
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
use local_cdo_ok\controllers\confirm_answers;

/**
 * External API для управления подтверждением ответов в анкетах.
 */
class external_confirm_answers extends external_api {

    /** @var string Название таблицы для подтверждения ответов */
    private const TABLE = 'local_cdo_ok_confirm_answers';

    /**
     * Описание параметров для create_update_confirm_answer.
     *
     * @return external_function_parameters
     */
    public static function create_update_confirm_answer_parameters(): external_function_parameters {
        global $USER;
        return new external_function_parameters([
            'data' => new external_single_structure([
                'user_id' => new external_value(PARAM_INT, 'ID пользователя', VALUE_DEFAULT, $USER->id),
                'integration' => new external_value(PARAM_TEXT, 'Тип интеграции', VALUE_REQUIRED),
                'status' => new external_value(PARAM_INT, 'Статус подтверждения', VALUE_REQUIRED),
            ], 'Данные для подтверждения ответов'),
        ]);
    }

    /**
     * Создание или обновление подтверждения ответов.
     *
     * @param array $data Данные для подтверждения
     * @return void
     */
    public static function create_update_confirm_answer(array $data): void {
        $params = self::validate_parameters(self::create_update_confirm_answer_parameters(), ['data' => $data]);
        $data = $params['data'];
        
        $confirmAnswer = new confirm_answers(self::TABLE);
        $confirmAnswer->update(
            $data,
            true,
            [
                'user_id' => $data['user_id'],
                'integration' => $data['integration'],
            ]
        );
    }

    /**
     * Описание возвращаемого значения для create_update_confirm_answer.
     *
     * @return null
     */
    public static function create_update_confirm_answer_returns(): ?external_value {
        return null;
    }

    /**
     * Описание параметров для get_confirm_answer.
     *
     * @return external_function_parameters
     */
    public static function get_confirm_answer_parameters(): external_function_parameters {
        global $USER;
        return new external_function_parameters([
            'params' => new external_single_structure([
                'integration' => new external_value(PARAM_TEXT, 'Тип интеграции', VALUE_OPTIONAL),
                'user_id' => new external_value(PARAM_INT, 'ID пользователя', VALUE_DEFAULT, $USER->id),
            ], 'Параметры для получения подтверждений', VALUE_DEFAULT, []),
        ]);
    }

    /**
     * Получение информации о подтверждении ответов.
     *
     * @param array $params Параметры запроса
     * @return array Массив подтверждений ответов
     */
    public static function get_confirm_answer(array $params = []): array {
        $params = self::validate_parameters(self::get_confirm_answer_parameters(), ['params' => $params]);
        
        $confirmAnswer = new confirm_answers(self::TABLE);
        return $confirmAnswer->get($params['params']);
    }

    /**
     * Описание возвращаемого значения для get_confirm_answer.
     *
     * @return external_multiple_structure
     */
    public static function get_confirm_answer_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'integration' => new external_value(PARAM_TEXT, 'Тип интеграции', VALUE_OPTIONAL),
                'user_id' => new external_value(PARAM_INT, 'ID пользователя', VALUE_OPTIONAL),
                'status' => new external_value(PARAM_BOOL, 'Статус подтверждения', VALUE_REQUIRED),
            ], 'Информация о подтверждении', VALUE_OPTIONAL),
            'Массив подтверждений ответов'
        );
    }
}
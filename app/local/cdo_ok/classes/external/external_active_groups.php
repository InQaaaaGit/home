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
 * External API для работы с активными группами анкет.
 *
 * @package    local_cdo_ok
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cdo_ok\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_cdo_ok\controllers\active_group_controller;

/**
 * External API для управления активными группами анкет.
 */
class external_active_groups extends external_api {

    /**
     * Описание параметров для create_update.
     *
     * @return external_function_parameters
     */
    public static function create_update_parameters(): external_function_parameters {
        return new external_function_parameters([
            'data' => new external_single_structure([
                'group_tab' => new external_value(PARAM_INT, 'Номер группы', VALUE_REQUIRED),
                'active' => new external_value(PARAM_INT, 'Статус активности', VALUE_REQUIRED),
            ], 'Данные для создания/обновления'),
        ]);
    }

    /**
     * Создание или обновление статуса активной группы.
     *
     * @param array $data Данные группы
     * @return bool Результат операции
     */
    public static function create_update(array $data): bool {
        $params = self::validate_parameters(self::create_update_parameters(), ['data' => $data]);
        
        $controller = new active_group_controller();
        return $controller->create_update($params['data']);
    }

    /**
     * Описание возвращаемого значения для create_update.
     *
     * @return external_value
     */
    public static function create_update_returns(): external_value {
        return new external_value(PARAM_BOOL, 'Результат операции', VALUE_REQUIRED);
    }

    /**
     * Описание параметров для get_active_group.
     *
     * @return external_function_parameters
     */
    public static function get_active_group_parameters(): external_function_parameters {
        return new external_function_parameters([
            'params' => new external_single_structure([
                'group_tab' => new external_value(PARAM_INT, 'Номер группы', VALUE_OPTIONAL),
            ], 'Параметры для получения активной группы', VALUE_DEFAULT, []),
        ]);
    }

    /**
     * Получение статуса активной группы.
     *
     * @param array $params Параметры запроса
     * @return bool Статус активности группы
     */
    public static function get_active_group(array $params = []): bool {
        $params = self::validate_parameters(self::get_active_group_parameters(), ['params' => $params]);
        
        $controller = new active_group_controller();
        return $controller->get($params['params']);
    }

    /**
     * Описание возвращаемого значения для get_active_group.
     *
     * @return external_value
     */
    public static function get_active_group_returns(): external_value {
        return new external_value(PARAM_BOOL, 'Статус активности группы', VALUE_REQUIRED);
    }
}
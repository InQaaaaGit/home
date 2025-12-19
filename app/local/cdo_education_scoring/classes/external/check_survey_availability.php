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
 * External API для проверки условий доступности анкеты.
 *
 * @package     local_cdo_education_scoring
 * @category    external
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cdo_education_scoring\external;

use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_type_response_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * External API для проверки условий доступности анкеты.
 *
 * Проверяет все условия, необходимые для доступа пользователя к анкете.
 * Передает параметры (surveyid, teacher_id, discipline_id, user_id) во внешний сервис.
 */
class check_survey_availability extends \external_api {

    /**
     * Описание параметров функции check_survey_availability.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'surveyid' => new \external_value(PARAM_INT, 'ID анкеты', VALUE_REQUIRED),
            'teacher_id' => new \external_value(PARAM_INT, 'ID преподавателя', VALUE_OPTIONAL, null),
            'discipline_id' => new \external_value(PARAM_TEXT, 'Код дисциплины', VALUE_OPTIONAL, null),
            'duration_days' => new \external_value(PARAM_INT, 'Длительность анкеты в днях', VALUE_OPTIONAL, null),
        ]);
    }

    /**
     * Проверка условий доступности анкеты.
     *
     * @param int $surveyid ID анкеты
     * @param int|null $teacher_id ID преподавателя (опционально)
     * @param string|null $discipline_id Код дисциплины (опционально)
     * @param int|null $duration_days Длительность анкеты в днях (опционально)
     * @return array Результат проверки доступности
     */
    public static function execute(
        int $surveyid,
        ?int $teacher_id = null,
        ?string $discipline_id = null,
        ?int $duration_days = null
    ): array {
        global $USER;

        // Валидация параметров.
        $params = self::validate_parameters(self::execute_parameters(), [
            'surveyid' => $surveyid,
            'teacher_id' => $teacher_id,
            'discipline_id' => $discipline_id,
            'duration_days' => $duration_days,
        ]);

        // Формируем параметры для запроса к внешнему сервису
        $requestParams = [
            'surveyid' => $params['surveyid'],
            'user_id' => $USER->id,
        ];

        // Добавляем teacher_id, если он передан
        if (!empty($params['teacher_id'])) {
            $requestParams['teacher_id'] = (int)$params['teacher_id'];
        }

        // Добавляем discipline_id, если он передан
        if (!empty($params['discipline_id'])) {
            $requestParams['discipline_id'] = trim($params['discipline_id']);
        }

        // Добавляем duration_days, если он передан
        if (!empty($params['duration_days'])) {
            $requestParams['duration_days'] = (int)$params['duration_days'];
        }

        try {
            // Передаем параметры в запрос к внешнему сервису
            $options = di::get_instance()->get_request_options();
            $options->set_properties($requestParams);

            $result = di::get_instance()
                ->get_request('check_survey_availability_for_scoring')
                ->request($options)
                ->get_request_result()
                ->to_array();

            // Если сервис вернул не массив, возвращаем ошибку
            if (!is_array($result)) {
                return [
                    'status' => false,
                    'message' => 'Ошибка при проверке доступности анкеты: неверный формат ответа сервиса',
                ];
            }

            // Возвращаем результат в формате DTO (только status и message)
            return [
                'status' => $result['status'] ?? false,
                'message' => $result['message'] ?? null,
            ];
        } catch (cdo_type_response_exception $e) {
            // Обработка ошибок типизации ответа от 1С
            error_log('check_survey_availability: cdo_type_response_exception - ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Ошибка ответа сервиса: ' . $e->getMessage(),
            ];
        } catch (\moodle_exception $e) {
            // Обработка ошибок Moodle
            error_log('check_survey_availability: Moodle exception - ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Ошибка проверки доступности: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            // Обработка общих исключений
            error_log('check_survey_availability: Exception - ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Ошибка при проверке доступности анкеты: ' . $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            // Обработка всех остальных ошибок (включая Error в PHP 7+)
            error_log('check_survey_availability: Throwable - ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Внутренняя ошибка сервера: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Описание возвращаемых данных функции check_survey_availability.
     *
     * @return \external_single_structure
     */
    public static function execute_returns(): \external_single_structure {
        return new \external_single_structure([
            'status' => new \external_value(
                PARAM_BOOL,
                'Статус доступности анкеты (true - доступна, false - недоступна)',
                VALUE_OPTIONAL
            ),
            'message' => new \external_value(
                PARAM_TEXT,
                'Сообщение о статусе доступности',
                VALUE_OPTIONAL
            ),
        ], 'Результат проверки доступности анкеты');
    }
}


<?php

namespace local_cdo_education_scoring\external;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../lib.php');

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use tool_cdo_config\di;

/**
 * External API для получения процента посещаемости студента.
 *
 * @package    local_cdo_education_scoring
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_percent_attendance extends external_api {

    /**
     * Описание входных параметров.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'user_id' => new external_value(PARAM_TEXT, 'ID студента'),
            'discipline_id' => new external_value(PARAM_TEXT, 'Код дисциплины'),
        ]);
    }

    /**
     * Получение процента посещаемости студента по дисциплине.
     *
     * @param string $userId ID студента
     * @param string $disciplineId Код дисциплины
     * @return array Данные о посещаемости
     */
    public static function execute(string $userId, string $disciplineId): array {
        global $USER;

        // Валидация параметров
        $params = self::validate_parameters(self::execute_parameters(), [
            'user_id' => $userId,
            'discipline_id' => $disciplineId,
        ]);

        // Проверка авторизации
        $context = \context_system::instance();
        self::validate_context($context);

        try {
            $requestParams = [
                'user_id' => $params['user_id'],
                'discipline_id' => $params['discipline_id'],
            ];

            $options = di::get_instance()->get_request_options();
            $options->set_properties($requestParams);

            $requestResult = di::get_instance()
                ->get_request('get_percent_attendance')
                ->request($options)
                ->get_request_result();

            // Получаем данные из результата
            if (method_exists($requestResult, 'to_array')) {
                $data = $requestResult->to_array();
            } elseif (is_array($requestResult)) {
                $data = $requestResult;
            } elseif (is_object($requestResult)) {
                $data = (array)$requestResult;
            } else {
                $data = [];
            }

            // Извлекаем процент посещаемости
            $percent = null;
            if (isset($data['percent'])) {
                $percent = (float)$data['percent'];
            } elseif (isset($data['attendance'])) {
                $percent = (float)$data['attendance'];
            } elseif (isset($data['value'])) {
                $percent = (float)$data['value'];
            } elseif (is_numeric($data)) {
                $percent = (float)$data;
            }

            return [
                'success' => true,
                'user_id' => $params['user_id'],
                'discipline_id' => $params['discipline_id'],
                'percent' => $percent,
                'error' => '',
            ];

        } catch (\Exception $e) {
            error_log('Ошибка получения посещаемости: ' . $e->getMessage());
            
            return [
                'success' => false,
                'user_id' => $params['user_id'],
                'discipline_id' => $params['discipline_id'],
                'percent' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Описание возвращаемого значения.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Успешность запроса'),
            'user_id' => new external_value(PARAM_TEXT, 'ID студента'),
            'discipline_id' => new external_value(PARAM_TEXT, 'Код дисциплины'),
            'percent' => new external_value(PARAM_FLOAT, 'Процент посещаемости', VALUE_OPTIONAL),
            'error' => new external_value(PARAM_TEXT, 'Сообщение об ошибке'),
        ]);
    }
}


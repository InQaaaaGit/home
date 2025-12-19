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
 * External API для получения данных студентов для отчета.
 *
 * @package     local_cdo_education_scoring
 * @category    external
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cdo_education_scoring\external;

use tool_cdo_config\di;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * External API для получения данных студентов для отчета.
 */
class get_students_report extends \external_api {

    /**
     * Описание параметров функции get_students_report.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'surveyid' => new \external_value(PARAM_INT, 'ID анкеты', VALUE_OPTIONAL, null),
            'discipline_id' => new \external_value(PARAM_TEXT, 'Код дисциплины', VALUE_OPTIONAL, null),
        ]);
    }

    /**
     * Получение данных студентов для отчета.
     *
     * @param int|null $surveyid ID анкеты (опционально)
     * @param string|null $discipline_id Код дисциплины (опционально)
     * @return array Список студентов с данными для отчета
     */
    public static function execute(?int $surveyid = null, ?string $discipline_id = null): array {
        global $USER;

        // Проверка прав доступа.
        $context = \context_system::instance();
        require_capability('local/cdo_education_scoring:manage', $context);

        // Валидация параметров.
        $params = self::validate_parameters(self::execute_parameters(), [
            'surveyid' => $surveyid,
            'discipline_id' => $discipline_id,
        ]);

        // Создаем ключ кэша на основе параметров запроса
        $cacheKey = self::get_cache_key($params['surveyid'], $params['discipline_id']);
        
        // Получаем кэш (application cache, время жизни 1 час)
        $cache = \cache::make('local_cdo_education_scoring', 'students_report');
        
        // Пытаемся получить данные из кэша
        $cachedData = $cache->get($cacheKey);
        if ($cachedData !== false) {
            return $cachedData;
        }

        // Если данных в кэше нет, запрашиваем у внешнего сервиса
        // Формируем параметры для запроса к внешнему сервису
        $requestParams = [];

        // Добавляем surveyid, если он передан
        if (!empty($params['surveyid'])) {
            $requestParams['surveyid'] = (int)$params['surveyid'];
        }

        // Добавляем discipline_id, если он передан
        if (!empty($params['discipline_id'])) {
            $requestParams['discipline_id'] = trim($params['discipline_id']);
        }

        // Передаем параметры в запрос к внешнему сервису
        $options = di::get_instance()->get_request_options();
        if (!empty($requestParams)) {
            $options->set_properties($requestParams);
        }

        try {
            $requestResult = di::get_instance()
                ->get_request('get_students_for_report')
                ->request($options)
                ->get_request_result()
                ->to_array();

            // Сохраняем результат в кэш на 1 час (3600 секунд)
            if (is_array($requestResult)) {
                $cache->set($cacheKey, $requestResult);
            }

            return $requestResult;
        } catch (\Exception $e) {
            // Если произошла ошибка с DTO, пробуем получить сырые данные напрямую
            // через другой способ, если он доступен
            try {
                // Пытаемся получить данные без преобразования в DTO
                $rawRequest = di::get_instance()->get_request('get_students_for_report');
                $rawOptions = di::get_instance()->get_request_options();
                if (!empty($requestParams)) {
                    $rawOptions->set_properties($requestParams);
                }
                
                // Если есть метод для получения сырых данных, используем его
                if (method_exists($rawRequest, 'request_raw')) {
                    $rawResult = $rawRequest->request_raw($rawOptions);
                    if (is_array($rawResult)) {
                        // Сохраняем в кэш
                        $cache->set($cacheKey, $rawResult);
                        return $rawResult;
                    }
                }
            } catch (\Exception $e2) {
                // Игнорируем вторую ошибку
            }
            
            // Логируем ошибку и возвращаем пустой массив
            error_log('Ошибка при получении данных студентов: ' . $e->getMessage());
            error_log('Трассировка: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Генерирует ключ кэша на основе параметров запроса.
     *
     * @param int|null $surveyid ID анкеты
     * @param string|null $discipline_id Код дисциплины
     * @return string Ключ кэша
     */
    private static function get_cache_key(?int $surveyid, ?string $discipline_id): string {
        $keyParts = [
            'students_report',
            'surveyid_' . ($surveyid ?? 'null'),
            'discipline_' . ($discipline_id ?? 'null'),
        ];
        return md5(implode('_', $keyParts));
    }

    /**
     * Описание возвращаемых данных функции get_students_report.
     *
     * @return \external_multiple_structure
     */
    public static function execute_returns(): \external_multiple_structure {
        return new \external_multiple_structure(
            new \external_single_structure([
                'id' => new \external_value(PARAM_TEXT, 'ID студента'),
                'fullname' => new \external_value(PARAM_TEXT, 'Полное имя студента'),
                'group' => new \external_value(PARAM_TEXT, 'Название группы'),
                'group_id' => new \external_value(PARAM_TEXT, 'ID группы'),
                'speciality' => new \external_value(PARAM_TEXT, 'Направление подготовки/Специальность'),
            ]),
            'Список студентов для отчета'
        );
    }
}


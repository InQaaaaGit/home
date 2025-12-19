<?php

namespace local_cdo_ok\services;

use coding_exception;
use curl;
use local_cdo_ok\DTO\student_info_dto;
use moodle_exception;
use tool_cdo_config\di;

class service_ok_1c
{
    /** @var int Таймаут запроса в секундах */
    private const REQUEST_TIMEOUT = 60;

    /**
     * Получает информацию о студентах из системы 1С
     *
     * @param string $ids JSON строка с массивом ID пользователей
     * @return student_info_dto[] Массив DTO с информацией о студентах
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_users_information($ids = ''): array
    {
        $start_time = microtime(true);
        
        try {
            $options = di::get_instance()->get_request_options();
            $options->set_parameters_in_json();
            $options->set_properties(($ids));

            // Устанавливаем таймаут для запроса если метод доступен
            if (method_exists($options, 'set_timeout')) {
                $options->set_timeout(self::REQUEST_TIMEOUT);
            }
            
            $result = di::get_instance()
                ->get_request('get_users_information_for_report')
                ->request($options)
                ->get_request_result()
                ->to_array();
            
            $execution_time = microtime(true) - $start_time;
            
            // Логируем успешный запрос
            debugging(
                sprintf('1C request completed in %.2f seconds', $execution_time),
                DEBUG_DEVELOPER
            );
            
            return $result;

        } catch (\Throwable $e) {
            $execution_time = microtime(true) - $start_time;
            
            // Детальное логирование ошибки
            debugging(
                sprintf(
                    '1C request failed after %.2f seconds: %s in %s:%d',
                    $execution_time,
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                ),
                DEBUG_DEVELOPER
            );
            
            // Возвращаем пустой массив вместо ошибки
            return [];
        }
    }
}
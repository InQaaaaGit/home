<?php

namespace local_cdo_ag_tools\integrations;

use local_cdo_ag_tools\config\grade_interceptor_config;
use local_cdo_ag_tools\helpers\grade_data_helper;
use stdClass;
use Exception;
use Throwable;
use tool_cdo_config\di;

/**
 * 1C Integration Class
 * 
 * Класс для интеграции с системой 1С
 * 
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class onec_integration
{
    /** @var array Очередь оценок для пакетной отправки */
    private static array $grade_queue = [];
    
    /**
     * Отправить оценку в 1С
     * 
     * @param array $grade_data Данные об оценке
     * @return array Успешность отправки
     */
    public static function send_grade_to_1c(array $grade_data): bool
    {

        try {
            $options = di::get_instance()->get_request_options();
            $options->set_parameters_in_json();
            $options->set_properties($grade_data);

            try {
                return di::get_instance()
                    ->get_request('set_grade')
                    ->request($options)
                    ->get_request_result()
                    ->to_array();

            } catch (Throwable $e) {
                return false;
            }

        } catch (Exception $e) {
            if (grade_interceptor_config::$onec_log_enabled) {
                debugging("Error sending grade to 1C: " . $e->getMessage(), DEBUG_DEVELOPER);
            }
            return false;
        }
    }
} 
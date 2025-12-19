<?php

namespace local_cdo_ag_tools\strategies;

use local_cdo_ag_tools\interfaces\grade_handler_interface;
use Throwable;
use tool_cdo_config\di;

/**
 * Direct Send Grade Strategy
 * 
 * Стратегия прямой отправки оценок в 1С
 * 
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class direct_send_grade_strategy implements grade_handler_interface
{
    /**
     * Обработать данные об оценке - отправить напрямую
     * 
     * @param array $grade_info Данные об оценке
     * @return bool Успешность обработки
     */
    public function handle_grade(array $grade_info): bool
    {
        try {
            // TODO: Здесь ваша логика прямой отправки в 1С
            // Например вызов API, веб-сервиса и т.д.
            
            // Пример заглушки
            $success = $this->send_to_1c_api($grade_info);
            
            if ($success) {
                return true;
            } else {
                return false;
            }
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Получить название стратегии
     * 
     * @return string Название стратегии
     */
    public function get_strategy_name(): string
    {
        return 'direct_send';
    }
    
    /**
     * Отправить данные в 1С API (заглушка)
     * 
     * @param array $grade_info Данные об оценке
     * @return array Успешность отправки
     */
    private function send_to_1c_api(array $grade_info): array
    {
        $options = di::get_instance()->get_request_options();
        $options->set_parameters_in_json();
        $options->set_properties($grade_info);

        try {
            return di::get_instance()
                ->get_request('set_grade')
                ->request($options)
                ->get_request_result()
                ->to_array();

        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }
}

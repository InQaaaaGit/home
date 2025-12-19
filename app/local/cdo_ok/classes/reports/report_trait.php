<?php

namespace local_cdo_ok\reports;

use coding_exception;
use dml_exception;
use local_cdo_ok\controllers\answers_controller;
use local_cdo_ok\services\integration;
use local_cdo_ok\services\service_ok_1c;
use moodle_exception;

trait report_trait
{
    /**
     * Поиск элемента по user_id в массиве объектов или ассоциативных массивов
     * 
     * @param int $id User ID для поиска
     * @param array $array Массив объектов или ассоциативных массивов
     * @return object|null Найденный элемент (преобразованный в объект) или null
     */
    public function find_object_by_id($id, $array) {
        foreach ($array as $item) {
            // Преобразуем массив в объект если нужно
            $object = is_array($item) ? (object)$item : $item;
            
            // Проверяем наличие user_id
            if (isset($object->user_id) && $object->user_id == $id) {
                return $object;
            }
        }
        return null;
    }
    public function get_info_from_1c() {
        return integration::test_case();
    }

    /**
     * @throws dml_exception
     */
    public static function prepared_ids_for_service(): array
    {
        $ac = new answers_controller();
        $idsRAW = $ac->get_all_user_ids_from_answers();
        $idsCLEAN = [];
        foreach ($idsRAW as $id) {
            $idsCLEAN[] = $id->user_id;
        }
        return $idsCLEAN;
    }

    /**
     * @throws moodle_exception
     * @throws coding_exception
     * @throws dml_exception
     */
    private function get_users_additional_info(): array
    {
        global $CFG;
        
        // Увеличиваем лимиты для генерации отчета
        @set_time_limit(300); // 5 минут
        @ini_set('memory_limit', '512M');
        
        $cache_key = 'cdo_ok_users_info_' . md5(serialize($this->get_cache_params()));
        $cache = \cache::make('local_cdo_ok', 'users_info');
        
        // Пытаемся получить данные из кеша
        $cached_data = $cache->get($cache_key);
        if ($cached_data !== false) {
            return $cached_data;
        }
        
        // Если в кеше нет - запрашиваем из 1С
        $user_ids = self::prepared_ids_for_service();
        
        try {
            $service = new service_ok_1c();
            $result = $service->get_users_information(json_encode($user_ids));
            
            // Проверяем на ошибки
            if (isset($result['error_message'])) {
                // Логируем ошибку
                debugging('Error fetching data from 1C: ' . $result['error_message'], DEBUG_DEVELOPER);
                return [];
            }
            
            // Сохраняем в кеш на 1 час
            $cache->set($cache_key, $result);
            
            return $result;
        } catch (\Throwable $e) {
            debugging('Exception fetching data from 1C: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return [];
        }
    }
    
    /**
     * Получает параметры для ключа кеша
     * Может быть переопределен в классах-наследниках для специфичного кеширования
     */
    protected function get_cache_params(): array
    {
        return ['timestamp' => date('Y-m-d-H')]; // Кешируем на час
    }
}
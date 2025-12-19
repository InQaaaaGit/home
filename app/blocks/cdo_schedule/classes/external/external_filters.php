<?php

namespace block_cdo_schedule\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_value;
use Throwable;
use tool_cdo_config\di;

class external_filters extends external_api
{
    /**
     * Получение списка курсов
     * 
     * @return array Список курсов
     */
    public static function get_courses(): array
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties([]);
        try {
            $request = di::get_instance()->get_request('get_courses_of_study')->request($options);
            $data = $request->get_request_result()->to_array();
            return $data;
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    /**
     * Получение списка групп по курсу обучения
     *
     * @param string $courseId ID курса обучения
     * @return array Список групп
     */
    public static function get_groups(string $courseId): array
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties(['courseId' => $courseId]);
        try {
            $request = di::get_instance()->get_request('get_groups_by_course')->request($options);
            $data = $request->get_request_result()->to_array();
            return $data;
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }

//        return [
//            ['id' => 1, 'name' => 'Группа А'],
//            ['id' => 2, 'name' => 'Группа Б'],
//            ['id' => 3, 'name' => 'Группа В'],
//            ['id' => 4, 'name' => 'Группа Г'],
//            ['id' => 5, 'name' => 'Группа Д']
//        ];
    }
    
    /**
     * Описание параметров для get_courses
     * 
     * @return external_function_parameters
     */
    public static function get_courses_parameters(): external_function_parameters
    {
        return new external_function_parameters([]);
    }
    
    /**
     * Описание параметров для get_groups
     * 
     * @return external_function_parameters
     */
    public static function get_groups_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'courseId' => new external_value(PARAM_RAW, 'ID курса обучения', VALUE_REQUIRED)
        ]);
    }
    
    /**
     * Описание возвращаемого значения для get_courses
     * 
     * @return external_multiple_structure
     */
    public static function get_courses_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_TEXT, 'ID курса'),
                'name' => new external_value(PARAM_TEXT, 'Название курса')
            ])
        );
    }
    
    /**
     * Описание возвращаемого значения для get_groups
     * 
     * @return external_multiple_structure
     */
    public static function get_groups_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_TEXT, 'ID группы'),
                'name' => new external_value(PARAM_TEXT, 'Название группы')
            ])
        );
    }
}

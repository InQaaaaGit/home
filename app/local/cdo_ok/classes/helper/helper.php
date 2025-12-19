<?php

namespace local_cdo_ok\helper;

use local_cdo_ok\reports\variants\report1;
use local_cdo_ok\reports\variants\report2;
use local_cdo_ok\reports\variants\report3;
use local_cdo_ok\reports\variants\report4;
use local_cdo_ok\reports\variants\report5;
use local_cdo_ok\reports\variants\report6;


class helper
{
    public static function get_years(): array
    {
        $current_year = date('Y');
        return range(2023, $current_year);
    }

    public static function get_reports(): array
    {
        $folderPath = __DIR__ . '/../reports/variants';

        // Получаем список файлов в папке
        /*$files = scandir($folderPath);
        $files_name = [];
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && $file !== '') {
                include_once $folderPath . '/' . $file;;
                # $files_name[] = pathinfo($file, PATHINFO_FILENAME);
                $class = pathinfo($file, PATHINFO_FILENAME);
                $reflectionClass = new ReflectionClass('local_cdo_ok\\reports\\variants\\' . $class);
                $instance = $reflectionClass->newInstance(new questions_controller(), new answers_controller());
                $method = $reflectionClass->getMethod('get_filename');
               # $filename = $method->invoke($instance);
                #var_dump($filename);

            }
        }*/
        // Удаляем из массива "." и ".." (ссылки на текущую и родительскую директории)

        return [
            ['value' => report1::class, 'text' => get_string('report:report1_name', 'local_cdo_ok')],
            ['value' => report2::class, 'text' => get_string('report:report2_name', 'local_cdo_ok')],
            ['value' => report3::class, 'text' => get_string('report:report3_name', 'local_cdo_ok')],
            ['value' => report4::class, 'text' => get_string('report:report4_name', 'local_cdo_ok')],
            ['value' => report5::class, 'text' => get_string('report:report5_name', 'local_cdo_ok')],
            ['value' => report6::class, 'text' => get_string('report:report6_name', 'local_cdo_ok')],
        ];
    }

    public static function get_strings(): array
    {
        return (array)get_strings(
            [
                'tabs:question_for_discipline',
                'tabs:question_for_education_program',
                'all',
                'active',
                'archive',
                'buttons:add',
                'buttons:construct',
                'fields:visible',
                'fields:question_name',
                'fields:type',
                'fields:parameters',
                'title_survey_full',
                'survey_is_active',
                'title_not_active_survey',
                'send_answers',
                'survey_is_confirmed',
                'active_survey'
            ],
            'local_cdo_ok'
        );
    }

    /**
     * Поиск элемента по user_id в массиве объектов или ассоциативных массивов
     * 
     * @param int $id User ID для поиска
     * @param array $array Массив объектов или ассоциативных массивов
     * @return object|null Найденный элемент (преобразованный в объект) или null
     */
    public static function findObjectById($id, $array)
    {
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
}
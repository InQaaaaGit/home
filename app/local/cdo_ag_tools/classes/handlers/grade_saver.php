<?php

namespace local_cdo_ag_tools\handlers;

use stdClass;
use Exception;

/**
 * Grade Saver Handler
 * 
 * Контроллер для сохранения данных об оценках для 1С
 * 
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class grade_saver
{
    /**
     * Сохранить данные об оценке для отправки в 1С
     * 
     * @param array $grade_info Данные об оценке
     * @return bool Успешность сохранения
     */
    public static function save_grade_for_1c(array $grade_info): bool
    {
        global $DB;
        
        try {
            // Валидируем входные данные
            if (!self::validate_grade_info($grade_info)) {
                return false;
            }
            
            // Проверяем существует ли уже запись для этого пользователя и курса
            $existing = $DB->get_record('local_cdo_ag_tools_grades_1c', [
                'course_id' => $grade_info['course_id'],
                'user_id' => $grade_info['user_id'],
                'section_id' => $grade_info['section_id'],
                'item_type' => $grade_info['item_type']
            ]);
            
            $current_time = time();
            
            if ($existing) {
                // Обновляем существующую запись
                $existing->grade = $grade_info['grade'];
                $existing->updated_at = $current_time;
                
                $result = $DB->update_record('local_cdo_ag_tools_grades_1c', $existing);
                
                return $result;
                
            } else {
                // Создаем новую запись
                $record = new stdClass();
                $record->course_id = $grade_info['course_id'];
                $record->user_id = $grade_info['user_id'];
                $record->grade = $grade_info['grade'];
                $record->section_id = $grade_info['section_id'];
                $record->item_type = $grade_info['item_type'];
                $record->created_at = $current_time;
                $record->updated_at = $current_time;
                
                $id = $DB->insert_record('local_cdo_ag_tools_grades_1c', $record);
                
                if ($id) {
                    return true;
                }
                
                return false;
            }
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Получить все сохраненные оценки для курса
     * 
     * @param int $course_id ID курса
     * @return array Массив записей об оценках
     */
    public static function get_grades_for_course(int $course_id): array
    {
        global $DB;
        
        try {
            return $DB->get_records('local_cdo_ag_tools_grades_1c', 
                ['course_id' => $course_id], 
                'updated_at DESC'
            );
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Получить все сохраненные оценки для пользователя
     * 
     * @param int $user_id ID пользователя
     * @return array Массив записей об оценках
     */
    public static function get_grades_for_user(int $user_id): array
    {
        global $DB;
        
        try {
            return $DB->get_records('local_cdo_ag_tools_grades_1c', 
                ['user_id' => $user_id], 
                'updated_at DESC'
            );
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Получить все оценки созданные после определенной даты
     * 
     * @param int $timestamp Timestamp после которого искать записи
     * @return array Массив записей об оценках
     */
    public static function get_grades_since(int $timestamp): array
    {
        global $DB;
        
        try {
            return $DB->get_records_select('local_cdo_ag_tools_grades_1c', 
                'created_at > ?', 
                [$timestamp], 
                'created_at ASC'
            );
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Удалить старые записи об оценках
     * 
     * @param int $days_old Количество дней после которых записи считаются старыми
     * @return int Количество удаленных записей
     */
    public static function cleanup_old_grades(int $days_old = 30): int
    {
        global $DB;
        
        try {
            $cutoff_time = time() - ($days_old * 24 * 60 * 60);
            
            $deleted = $DB->delete_records_select('local_cdo_ag_tools_grades_1c', 
                'created_at < ?', 
                [$cutoff_time]
            );
            
            return $deleted;
            
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Удалить запись об оценке
     *
     * @param int $id ID записи
     * @return bool Успешность удаления
     */
    public static function delete_grade(int $id): bool
    {
        global $DB;

        try {
            return $DB->delete_records('local_cdo_ag_tools_grades_1c', ['id' => $id]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Валидация данных об оценке
     * 
     * @param array $grade_info Данные об оценке
     * @return bool True если данные валидны
     */
    private static function validate_grade_info(array $grade_info): bool
    {
        // Проверяем обязательные поля
        $required_fields = ['course_id', 'user_id', 'grade', 'item_type'];
        
        foreach ($required_fields as $field) {
            if (!isset($grade_info[$field]) || $grade_info[$field] === '') {
                return false;
            }
        }
        
        // Проверяем типы данных
        if (!is_numeric($grade_info['course_id']) || 
            !is_numeric($grade_info['user_id']) || 
            !is_numeric($grade_info['grade'])) {
            return false;
        }
        
        // section_id может быть null
        if (isset($grade_info['section_id']) && 
            $grade_info['section_id'] !== null && 
            !is_numeric($grade_info['section_id'])) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Получить статистику по сохраненным оценкам
     * 
     * @return stdClass Статистика
     */
    public static function get_statistics(): stdClass
    {
        global $DB;
        
        $stats = new stdClass();
        
        try {
            // Общее количество записей
            $stats->total_records = $DB->count_records('local_cdo_ag_tools_grades_1c');
            
            // Количество уникальных пользователей
            $stats->unique_users = $DB->count_records_sql(
                'SELECT COUNT(DISTINCT user_id) FROM {local_cdo_ag_tools_grades_1c}'
            );
            
            // Количество уникальных курсов
            $stats->unique_courses = $DB->count_records_sql(
                'SELECT COUNT(DISTINCT course_id) FROM {local_cdo_ag_tools_grades_1c}'
            );
            
            // Записи за последние 24 часа
            $yesterday = time() - (24 * 60 * 60);
            $stats->records_last_24h = $DB->count_records_select('local_cdo_ag_tools_grades_1c', 
                'created_at > ?', [$yesterday]
            );
            
        } catch (Exception $e) {
            $stats->total_records = 0;
            $stats->unique_users = 0;
            $stats->unique_courses = 0;
            $stats->records_last_24h = 0;
        }
        
        return $stats;
    }
}

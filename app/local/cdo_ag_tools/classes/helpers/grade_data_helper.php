<?php

namespace local_cdo_ag_tools\helpers;

use grade_item;
use grade_grade;
use user;
use context_course;
use stdClass;

/**
 * Grade Data Helper
 * 
 * Вспомогательный класс для работы с данными оценок
 * 
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class grade_data_helper
{
    /**
     * Получает детальную информацию об оценке
     * 
     * @param array $grade_data Данные об оценке из event
     * @return stdClass Расширенная информация об оценке
     */
    public static function get_detailed_grade_info(array $grade_data): stdClass
    {
        $details = new stdClass();
        
        // Базовая информация
        $details->event_type = $grade_data['event_type'];
        $details->user_id = $grade_data['user_id'] ?? null;
        $details->course_id = $grade_data['course_id'] ?? null;
        $details->timestamp = $grade_data['timestamp'] ?? time();
        
        // Информация о пользователе
        if (!empty($details->user_id)) {
            $details->user = self::get_user_info($details->user_id);
        }
        
        // Информация о курсе
        if (!empty($details->course_id)) {
            $details->course = get_course($details->course_id);
        }
        
        // Информация об оценке
        if (isset($grade_data['grade_item']) && $grade_data['grade_item']) {
            $details->grade_item = $grade_data['grade_item'];
            $details->grade_item_name = $grade_data['grade_item']->get_name();
            $details->grade_item_type = $grade_data['grade_item']->itemtype;
            $details->grade_item_module = $grade_data['grade_item']->itemmodule;
        }
        
        // Числовые значения оценки
        $details->final_grade = $grade_data['final_grade'] ?? null;
        $details->raw_grade = $grade_data['raw_grade'] ?? null;
        $details->grade_min = $grade_data['grade_min'] ?? 0;
        $details->grade_max = $grade_data['grade_max'] ?? 100;
        
        // Дополнительные вычисления
        if ($details->final_grade !== null) {
            $details->grade_percentage = self::calculate_grade_percentage(
                $details->final_grade, 
                $details->grade_min, 
                $details->grade_max
            );
            $details->is_passing_grade = self::is_passing_grade(
                $details->final_grade, 
                $details->grade_max
            );
        }
        
        return $details;
    }
    
    /**
     * Получает информацию о пользователе
     * 
     * @param int $user_id ID пользователя
     * @return stdClass|null Информация о пользователе
     */
    public static function get_user_info(int $user_id): ?stdClass
    {
        global $DB;
        
        return $DB->get_record('user', ['id' => $user_id], 
            'id, firstname, lastname, email, username');
    }
    
    /**
     * Вычисляет процент оценки
     * 
     * @param float $grade Оценка
     * @param float $min_grade Минимальная оценка
     * @param float $max_grade Максимальная оценка
     * @return float Процент (0-100)
     */
    public static function calculate_grade_percentage(float $grade, float $min_grade, float $max_grade): float
    {
        if ($max_grade === $min_grade) {
            return 0;
        }
        
        return round((($grade - $min_grade) / ($max_grade - $min_grade)) * 100, 2);
    }
    
    /**
     * Проверяет является ли оценка проходной
     * 
     * @param float $grade Оценка
     * @param float $max_grade Максимальная оценка
     * @param float $passing_threshold Порог прохождения (по умолчанию 60%)
     * @return bool True если оценка проходная
     */
    public static function is_passing_grade(float $grade, float $max_grade, float $passing_threshold = 0.6): bool
    {
        if ($max_grade <= 0) {
            return false;
        }
        
        $percentage = $grade / $max_grade;
        return $percentage >= $passing_threshold;
    }
    
    /**
     * Форматирует оценку для отображения
     * 
     * @param float|null $grade Оценка
     * @param int $decimals Количество знаков после запятой
     * @return string Отформатированная оценка
     */
    public static function format_grade(?float $grade, int $decimals = 2): string
    {
        if ($grade === null) {
            return '-';
        }
        
        return number_format($grade, $decimals);
    }
    
    /**
     * Получает статистику оценок пользователя в курсе
     * 
     * @param int $user_id ID пользователя
     * @param int $course_id ID курса
     * @return stdClass Статистика оценок
     */
    public static function get_user_grade_statistics(int $user_id, int $course_id): stdClass
    {
        global $DB;
        
        $stats = new stdClass();
        
        $sql = "SELECT 
                    COUNT(*) as total_grades,
                    AVG(finalgrade) as average_grade,
                    MAX(finalgrade) as highest_grade,
                    MIN(finalgrade) as lowest_grade
                FROM {grade_grades} gg
                JOIN {grade_items} gi ON gg.itemid = gi.id
                WHERE gg.userid = :userid 
                AND gi.courseid = :courseid 
                AND gg.finalgrade IS NOT NULL
                AND gi.itemtype != 'course'
                AND gi.itemtype != 'category'";
        
        $result = $DB->get_record_sql($sql, [
            'userid' => $user_id,
            'courseid' => $course_id
        ]);
        
        $stats->total_grades = (int)($result->total_grades ?? 0);
        $stats->average_grade = round($result->average_grade ?? 0, 2);
        $stats->highest_grade = $result->highest_grade ?? 0;
        $stats->lowest_grade = $result->lowest_grade ?? 0;
        
        return $stats;
    }
    
    /**
     * Проверяет права доступа к оценке
     * 
     * @param int $user_id ID пользователя
     * @param int $course_id ID курса
     * @param int $context_user_id ID пользователя в контексте
     * @return bool True если есть права доступа
     */
    public static function can_access_grade(int $user_id, int $course_id, int $context_user_id): bool
    {
        $context = context_course::instance($course_id);
        
        // Пользователь может видеть свои оценки
        if ($user_id === $context_user_id) {
            return true;
        }
        
        // Преподаватель может видеть оценки студентов
        return has_capability('moodle/grade:viewall', $context, $user_id);
    }
    
    /**
     * Логирует событие оценки
     * 
     * @param array $grade_data Данные об оценке
     * @param string $action Действие (before_processing, processing, after_processing)
     * @return void
     */
    public static function log_grade_event(array $grade_data, string $action): void
    {
        $message = sprintf(
            "Grade %s: Event=%s, User=%d, Course=%d, Grade=%s",
            $action,
            $grade_data['event_type'] ?? 'unknown',
            $grade_data['user_id'] ?? 0,
            $grade_data['course_id'] ?? 0,
            isset($grade_data['final_grade']) ? self::format_grade($grade_data['final_grade']) : 'N/A'
        );
        
        debugging($message, DEBUG_DEVELOPER);
        
        // Можно добавить запись в custom log table если необходимо
        // self::write_to_custom_log($grade_data, $action);
    }
    
    /**
     * Проверяет нужно ли обрабатывать данную оценку
     * 
     * @param array $grade_data Данные об оценке
     * @return bool True если оценку нужно обрабатывать
     */
    public static function should_process_grade(array $grade_data): bool
    {
        // Не обрабатываем оценки категорий и курса
        if (isset($grade_data['grade_item'])) {
            $item_type = $grade_data['grade_item']->itemtype;
            if ($item_type === 'category' || $item_type === 'course') {
                return false;
            }
        }
        
        // Не обрабатываем пустые оценки
        if (empty($grade_data['final_grade']) && $grade_data['final_grade'] !== 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Получить ID секции курса для элемента оценки
     * 
     * @param grade_item $grade_item Элемент оценки
     * @param int $course_id ID курса
     * @return int|null ID секции или null если не найдена
     */
    public static function get_section_id_for_grade_item($grade_item, int $course_id): ?int
    {
        global $DB;
        
        // Проверяем что у grade_item есть модуль и инстанс
        if (!$grade_item->itemmodule || !$grade_item->iteminstance) {
            return null;
        }
        
        try {
            // Находим course_module по модулю и инстансу
            $cm = $DB->get_record('course_modules', [
                'course' => $course_id,
                'module' => $DB->get_field('modules', 'id', ['name' => $grade_item->itemmodule]),
                'instance' => $grade_item->iteminstance
            ]);
            
            if ($cm) {
                return (int)$cm->section;
            }
            
        } catch (\Exception $e) {
            debugging('Error getting section_id for grade_item: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
        
        return null;
    }
} 
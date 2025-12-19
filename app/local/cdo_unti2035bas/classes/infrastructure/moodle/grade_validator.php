<?php

namespace local_cdo_unti2035bas\infrastructure\moodle;

/**
 * Класс для валидации данных оценок
 * Отвечает только за проверку валидности данных и бизнес-правил
 */
class grade_validator {
    
    /**
     * Проверка, нужно ли отправлять xAPI statement для данной оценки
     * 
     * @param array $grade_data
     * @return bool
     */
    public static function should_send_xapi_statement(array $grade_data): bool {
        // Проверяем базовые условия
        if (!self::has_required_fields($grade_data)) {
            return false;
        }
        
        // Проверяем бизнес-правила
        if (!self::meets_business_rules($grade_data)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Проверка наличия обязательных полей
     * 
     * @param array $grade_data
     * @return bool
     */
    private static function has_required_fields(array $grade_data): bool {
        $required_fields = ['userid', 'itemid', 'courseid'];
        
        foreach ($required_fields as $field) {
            if (empty($grade_data[$field])) {
                debugging("cdo_unti2035bas: Отсутствует обязательное поле {$field}", DEBUG_DEVELOPER);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Проверка бизнес-правил
     * 
     * @param array $grade_data
     * @return bool
     */
    private static function meets_business_rules(array $grade_data): bool {
        // TODO: Добавить специфичные бизнес-правила
        // Например:
        // - Проверка типа элемента оценки
        // - Проверка курса (только для определенных курсов)
        // - Проверка пользователя (исключить системных пользователей)
        // - Проверка значения оценки (должно быть валидным числом)
        
        // Проверяем, что userid не является системным пользователем
        if ($grade_data['userid'] < 2) {
            debugging("cdo_unti2035bas: Системный пользователь, пропускаем", DEBUG_DEVELOPER);
            return false;
        }
        
        // Проверяем, что courseid валидный
        if ($grade_data['courseid'] < 2) {
            debugging("cdo_unti2035bas: Невалидный courseid", DEBUG_DEVELOPER);
            return false;
        }
        
        // Проверяем значение оценки (если есть)
        if (isset($grade_data['grade']) && !self::is_valid_grade_value($grade_data['grade'])) {
            debugging("cdo_unti2035bas: Невалидное значение оценки", DEBUG_DEVELOPER);
            return false;
        }



        return true;
    }
    
    /**
     * Проверка валидности значения оценки
     * 
     * @param mixed $grade_value
     * @return bool
     */
    private static function is_valid_grade_value($grade_value): bool {
        // Оценка должна быть числом или null
        if ($grade_value === null) {
            return true; // null допустим (например, для удаленных оценок)
        }
        
        if (!is_numeric($grade_value)) {
            return false;
        }
        
        // Оценка не должна быть отрицательной
        if ($grade_value < 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Проверка, является ли элемент оценки подходящим для xAPI
     * 
     * @param array $grade_data
     * @return bool
     */
    public static function is_suitable_grade_item(array $grade_data): bool {
        // TODO: Добавить проверку типа элемента
        // Например, исключить определенные типы элементов
        
        $excluded_types = ['category', 'course'];
        $excluded_modules = ['forum', 'chat']; // если нужно исключить определенные модули
        
        if (in_array($grade_data['itemtype'] ?? '', $excluded_types)) {
            debugging("cdo_unti2035bas: Исключенный тип элемента: {$grade_data['itemtype']}", DEBUG_DEVELOPER);
            return false;
        }
        
        if (in_array($grade_data['itemmodule'] ?? '', $excluded_modules)) {
            debugging("cdo_unti2035bas: Исключенный модуль: {$grade_data['itemmodule']}", DEBUG_DEVELOPER);
            return false;
        }
        
        return true;
    }
} 
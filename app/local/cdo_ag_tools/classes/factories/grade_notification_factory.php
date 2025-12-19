<?php

namespace local_cdo_ag_tools\factories;

use local_cdo_ag_tools\interfaces\grade_notification_handler;
use local_cdo_ag_tools\handlers\grade_notification_default_handler;
use moodle_exception;

/**
 * Фабрика для создания обработчиков уведомлений
 */
class grade_notification_factory {
    /**
     * Создает обработчик уведомлений для указанного типа элемента курса
     * 
     * @param string $modtype Тип элемента курса (например, 'assign', 'quiz', 'lesson')
     * @return grade_notification_handler
     * @throws moodle_exception
     */
    public static function create_handler(string $modtype): grade_notification_handler {
        $classname = "\\local_cdo_ag_tools\\handlers\\grade_notification_{$modtype}_handler";
        
        if (class_exists($classname)) {
            return new $classname();
        }
        
        // Если нет специального обработчика, используем базовый
        return new grade_notification_default_handler();
    }
} 
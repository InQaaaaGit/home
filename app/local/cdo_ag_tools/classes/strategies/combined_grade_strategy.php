<?php

namespace local_cdo_ag_tools\strategies;

use local_cdo_ag_tools\interfaces\grade_handler_interface;

/**
 * Combined Grade Strategy
 * 
 * Комбинированная стратегия - сохранение в БД + прямая отправка
 * 
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class combined_grade_strategy implements grade_handler_interface
{
    /** @var database_grade_strategy */
    private $database_strategy;
    
    /** @var direct_send_grade_strategy */
    private $direct_strategy;
    
    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->database_strategy = new database_grade_strategy();
        $this->direct_strategy = new direct_send_grade_strategy();
    }
    
    /**
     * Обработать данные об оценке - сохранить в БД И отправить напрямую
     * 
     * @param array $grade_info Данные об оценке
     * @return bool Успешность обработки (true если хотя бы одна операция успешна)
     */
    public function handle_grade(array $grade_info): bool
    {
        $database_success = false;
        $direct_success = false;
        
        // Сохраняем в базу данных
        try {
            $database_success = $this->database_strategy->handle_grade($grade_info);
        } catch (\Exception $e) {
            // Логируем ошибку без вывода в дебаггер
        }
        
        // Отправляем напрямую
        try {
            $direct_success = $this->direct_strategy->handle_grade($grade_info);
        } catch (\Exception $e) {
            // Логируем ошибку без вывода в дебаггер
        }
        
        // Возвращаем true если хотя бы одна операция успешна
        return $database_success || $direct_success;
    }
    
    /**
     * Получить название стратегии
     * 
     * @return string Название стратегии
     */
    public function get_strategy_name(): string
    {
        return 'combined';
    }
}

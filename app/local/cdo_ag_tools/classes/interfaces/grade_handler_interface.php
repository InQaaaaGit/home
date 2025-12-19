<?php

namespace local_cdo_ag_tools\interfaces;

/**
 * Interface for Grade Handlers
 * 
 * Интерфейс для различных стратегий обработки оценок
 * 
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
interface grade_handler_interface
{
    /**
     * Обработать данные об оценке
     * 
     * @param array $grade_info Данные об оценке
     * @return bool Успешность обработки
     */
    public function handle_grade(array $grade_info): bool;
    
    /**
     * Получить название стратегии
     * 
     * @return string Название стратегии
     */
    public function get_strategy_name(): string;
} 
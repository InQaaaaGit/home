<?php

namespace local_cdo_ag_tools\strategies;

use local_cdo_ag_tools\interfaces\grade_handler_interface;
use local_cdo_ag_tools\handlers\grade_saver;

/**
 * Database Grade Strategy
 * 
 * Стратегия сохранения оценок в базу данных
 * 
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class database_grade_strategy implements grade_handler_interface
{
    /**
     * Обработать данные об оценке - сохранить в БД
     * 
     * @param array $grade_info Данные об оценке
     * @return bool Успешность обработки
     */
    public function handle_grade(array $grade_info): bool
    {
        return grade_saver::save_grade_for_1c($grade_info);
    }
    
    /**
     * Получить название стратегии
     * 
     * @return string Название стратегии
     */
    public function get_strategy_name(): string
    {
        return 'database';
    }
}

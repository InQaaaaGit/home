<?php

namespace local_cdo_ag_tools\factories;

use local_cdo_ag_tools\interfaces\grade_handler_interface;
use local_cdo_ag_tools\strategies\database_grade_strategy;
use local_cdo_ag_tools\strategies\direct_send_grade_strategy;
use local_cdo_ag_tools\strategies\combined_grade_strategy;

/**
 * Grade Strategy Factory
 * 
 * Фабрика для создания стратегий обработки оценок
 * 
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class grade_strategy_factory
{
    /** @var array Доступные стратегии */
    private static array $available_strategies = [
        'database' => database_grade_strategy::class,
        'direct_send' => direct_send_grade_strategy::class,
        'combined' => combined_grade_strategy::class,
    ];
    
    /**
     * Создать стратегию на основе настроек
     * 
     * @return grade_handler_interface
     */
    public static function create(): grade_handler_interface
    {
        $strategy_name = get_config('local_cdo_ag_tools', 'grade_handling_strategy') ?: 'database';
        
        return self::create_by_name($strategy_name);
    }
    
    /**
     * Создать стратегию по имени
     * 
     * @param string $strategy_name Имя стратегии
     * @return grade_handler_interface
     * @throws \InvalidArgumentException
     */
    public static function create_by_name(string $strategy_name): grade_handler_interface
    {
        if (!isset(self::$available_strategies[$strategy_name])) {
            $strategy_name = 'database';
        }
        
        $strategy_class = self::$available_strategies[$strategy_name];
        
        return new $strategy_class();
    }
    
    /**
     * Получить список доступных стратегий
     * 
     * @return array Массив [имя_стратегии => описание]
     */
    public static function get_available_strategies(): array
    {
        return [
            'database' => 'Сохранение в базу данных',
            'direct_send' => 'Прямая отправка в 1С',
            'combined' => 'Сохранение в БД + прямая отправка',
        ];
    }
    
    /**
     * Проверить существует ли стратегия
     * 
     * @param string $strategy_name Имя стратегии
     * @return bool
     */
    public static function strategy_exists(string $strategy_name): bool
    {
        return isset(self::$available_strategies[$strategy_name]);
    }
}

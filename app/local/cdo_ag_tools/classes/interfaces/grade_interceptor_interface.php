<?php

namespace local_cdo_ag_tools\interfaces;

use core\event\user_graded;
use core\event\grade_item_updated;
use mod_assign\event\submission_graded;

/**
 * Grade Interceptor Interface
 * 
 * Интерфейс для перехватчиков событий оценивания
 * 
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
interface grade_interceptor_interface
{
    /**
     * Хук перед обработкой оценки
     * 
     * @param array $grade_data Данные об оценке
     * @return void
     */
    public static function before_grade_processing(array $grade_data): void;
    
    /**
     * Основная обработка оценки
     * 
     * @param array $grade_data Данные об оценке
     * @return void
     */
    public static function process_grade_update(array $grade_data): void;
    
    /**
     * Хук после обработки оценки
     * 
     * @param array $grade_data Данные об оценке
     * @return void
     */
    public static function after_grade_processing(array $grade_data): void;
    
    /**
     * Хук перед обработкой grade item
     * 
     * @param array $grade_data Данные о grade item
     * @return void
     */
    public static function before_grade_item_processing(array $grade_data): void;
    
    /**
     * Обработка grade item
     * 
     * @param array $grade_data Данные о grade item
     * @return void
     */
    public static function process_grade_item_update(array $grade_data): void;
    
    /**
     * Хук после обработки grade item
     * 
     * @param array $grade_data Данные о grade item
     * @return void
     */
    public static function after_grade_item_processing(array $grade_data): void;
    
    /**
     * Хук перед обработкой submission grade
     * 
     * @param array $grade_data Данные о submission grade
     * @return void
     */
    public static function before_submission_grade_processing(array $grade_data): void;
    
    /**
     * Обработка submission grade
     * 
     * @param array $grade_data Данные о submission grade
     * @return void
     */
    public static function process_submission_grade(array $grade_data): void;
    
    /**
     * Хук после обработки submission grade
     * 
     * @param array $grade_data Данные о submission grade
     * @return void
     */
    public static function after_submission_grade_processing(array $grade_data): void;
} 
<?php

namespace local_cdo_ag_tools\observers;

use core\event\user_graded;
use core\event\grade_item_updated;
use Exception;
use local_cdo_ag_tools\factories\grade_strategy_factory;
use mod_assign\event\submission_graded;
use grade_item;
use grade_grade;
use local_cdo_ag_tools\interfaces\grade_interceptor_interface;
use local_cdo_ag_tools\helpers\grade_data_helper;
use local_cdo_ag_tools\config\grade_interceptor_config;
use local_cdo_ag_tools\integrations\onec_integration;
use moodle_exception;
use stdClass;

/**
 * Grade Interceptor Observer
 * 
 * Универсальный хук для перехвата различных событий выставления оценок
 * 
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class grade_interceptor implements grade_interceptor_interface
{
    /**
     * Перехватчик события выставления оценки пользователю
     * 
     * @param user_graded $event
     * @return void
     * @throws moodle_exception
     */
    public static function observe_user_graded(user_graded $event): void
    {
        if (get_config('local_cdo_ag_tools', 'send_grades_to_1c')) {
            try {
                $event_data = $event->get_data();
                $course_id = $event_data['courseid'];
                $related_user_id = $event_data['relateduserid'];
                $grade_item = grade_item::fetch(['id' => $event_data['other']['itemid']]);
                // Собираем информацию для отправки в 1С
                $grade_info = [
                    "course_id" => (string)$course_id,
                    "user_id" => (string)$related_user_id,
                    "grade" => $event_data['other']['finalgrade'],
                    "section_id" => grade_data_helper::get_section_id_for_grade_item($grade_item, $course_id),
                    "item_type" => $grade_item->itemtype
                ];
                // Получаем стратегию обработки и выполняем
                $strategy = grade_strategy_factory::create();
                $strategy->handle_grade($grade_info);

            } catch (Exception $e) {
                // Логируем ошибку без вывода в дебаггер
            }
        }
    }
    
    /**
     * Перехватчик события обновления grade item
     * 
     * @param grade_item_updated $event
     * @return void
     */
    public static function observe_grade_item_updated(grade_item_updated $event): void
    {
        $grade_data = self::extract_grade_item_data_from_event($event);
        
        self::before_grade_item_processing($grade_data);
        self::process_grade_item_update($grade_data);
        self::after_grade_item_processing($grade_data);
    }
    
    /**
     * Перехватчик события оценивания задания (assignment)
     * 
     * @param submission_graded $event
     * @return void
     */
    public static function observe_submission_graded(submission_graded $event): void
    {
        $grade_data = self::extract_submission_grade_data_from_event($event);
        
        self::before_submission_grade_processing($grade_data);
        self::process_submission_grade($grade_data);
        self::after_submission_grade_processing($grade_data);
    }
    
    /**
     * Извлекает данные об оценке из события user_graded
     * 
     * @param user_graded $event
     * @return array
     */
    private static function extract_grade_data_from_event(user_graded $event): array
    {
        $event_data = $event->get_data();
        
        $grade_item = grade_item::fetch(['id' => $event_data['other']['itemid']]);
        $grade_grade = new grade_grade(['itemid' => $event_data['other']['itemid'], 
                                      'userid' => $event_data['relateduserid']]);
        
        return [
            'event_type' => 'user_graded',
            'event_data' => $event_data,
            'user_id' => $event_data['relateduserid'],
            'course_id' => $event_data['courseid'],
            'grade_item' => $grade_item,
            'grade_grade' => $grade_grade,
            'final_grade' => $event_data['other']['finalgrade'] ?? null,
            'raw_grade' => $event_data['other']['rawgrade'] ?? null,
            'grade_min' => $event_data['other']['grademin'] ?? null,
            'grade_max' => $event_data['other']['grademax'] ?? null,
            'timestamp' => $event->timecreated,
            'context' => $event->get_context()
        ];
    }
    
    /**
     * Извлекает данные из события grade_item_updated
     * 
     * @param grade_item_updated $event
     * @return array
     */
    private static function extract_grade_item_data_from_event(grade_item_updated $event): array
    {
        $event_data = $event->get_data();
        
        return [
            'event_type' => 'grade_item_updated',
            'event_data' => $event_data,
            'grade_item_id' => $event_data['objectid'],
            'course_id' => $event_data['courseid'],
            'timestamp' => $event->timecreated,
            'context' => $event->get_context()
        ];
    }
    
    /**
     * Извлекает данные из события submission_graded
     * 
     * @param submission_graded $event
     * @return array
     */
    private static function extract_submission_grade_data_from_event(submission_graded $event): array
    {
        $event_data = $event->get_data();
        
        return [
            'event_type' => 'submission_graded',
            'event_data' => $event_data,
            'user_id' => $event_data['relateduserid'],
            'course_id' => $event_data['courseid'],
            'assignment_id' => $event_data['other']['assignmentid'] ?? null,
            'submission_id' => $event_data['objectid'],
            'timestamp' => $event->timecreated,
            'context' => $event->get_context()
        ];
    }
    
    /**
     * Хук, вызываемый перед обработкой оценки
     * 
     * @param array $grade_data
     * @return void
     */
    public static function before_grade_processing(array $grade_data): void
    {
        // Проверяем нужно ли обрабатывать эту оценку
        if (!grade_data_helper::should_process_grade($grade_data)) {
            return;
        }
        
        // Логируем событие
        grade_data_helper::log_grade_event($grade_data, 'before_processing');
        
        // TODO: Добавьте свою логику перед обработкой оценки
        // Например: валидация, проверка прав доступа, уведомления
        
        // Пример получения детальной информации об оценке
        $detailed_info = grade_data_helper::get_detailed_grade_info($grade_data);
        
        // Можете использовать детальную информацию для ваших нужд
        // Логика обработки без дебаггеров
    }
    
    /**
     * Основная обработка обновления оценки
     * 
     * @param array $grade_data
     * @return void
     */
    public static function process_grade_update(array $grade_data): void
    {
        // Логируем событие обработки
        grade_data_helper::log_grade_event($grade_data, 'processing');
        
        // Получаем детальную информацию об оценке
        $detailed_info = grade_data_helper::get_detailed_grade_info($grade_data);
        
        // TODO: Здесь добавьте вашу основную логику обработки оценки
        
        // Примеры того что можно делать:
        
        // 1. Анализ успеваемости
        if (!empty($detailed_info->user_id) && !empty($detailed_info->course_id)) {
            $user_stats = grade_data_helper::get_user_grade_statistics(
                $detailed_info->user_id, 
                $detailed_info->course_id
            );
            
            // Логика обработки статистики пользователя без дебаггеров
        }
        
        // 2. Проверка проходной оценки
        if ($detailed_info->final_grade !== null && isset($detailed_info->is_passing_grade)) {
            if (!$detailed_info->is_passing_grade) {
                // Можно отправить уведомление о низкой оценке
                // Логика обработки низкой оценки без дебаггеров
            }
        }
        
        // 3. Кастомная логика в зависимости от типа элемента оценки
        if (isset($detailed_info->grade_item_type)) {
            switch ($detailed_info->grade_item_type) {
                case 'mod':
                    // Обработка модульных оценок (задания, тесты и т.д.)
                    self::process_module_grade($detailed_info);
                    break;
                case 'manual':
                    // Обработка ручных оценок
                    self::process_manual_grade($detailed_info);
                    break;
                default:
                    // Обработка других типов оценок
                    break;
            }
        }
        
        // Логика завершения обработки оценки без дебаггеров
    }
    
    /**
     * Обработка модульных оценок
     * 
     * @param stdClass $detailed_info Детальная информация об оценке
     * @return void
     */
    private static function process_module_grade($detailed_info): void
    {
        // TODO: Логика для модульных оценок
        // Логика обработки модульной оценки без дебаггеров
    }
    
    /**
     * Обработка ручных оценок
     * 
     * @param stdClass $detailed_info Детальная информация об оценке
     * @return void
     */
    private static function process_manual_grade($detailed_info): void
    {
        // TODO: Логика для ручных оценок
        // Логика обработки ручной оценки без дебаггеров
    }
    
    /**
     * Хук, вызываемый после обработки оценки
     * 
     * @param array $grade_data
     * @return void
     */
    public static function after_grade_processing(array $grade_data): void
    {
        // Логируем завершение обработки
        grade_data_helper::log_grade_event($grade_data, 'after_processing');
        
        // TODO: Добавьте свою логику после обработки оценки
        // Например: уведомления, аналитика, отчеты, синхронизация с внешними системами
        
        // Пример: Отправка уведомлений
        self::handle_post_grade_notifications($grade_data);
        
        // Пример: Обновление статистики
        self::update_grade_analytics($grade_data);
        
        // Отправка в 1С
        self::handle_1c_integration($grade_data);
        
        // Логика завершения обработки оценки без дебаггеров
    }
    
    /**
     * Обработка уведомлений после выставления оценки
     * 
     * @param array $grade_data
     * @return void
     */
    private static function handle_post_grade_notifications(array $grade_data): void
    {
        // TODO: Реализуйте логику уведомлений
        // Например: email уведомления, push уведомления, внутренние сообщения
        
        // Логика обработки уведомлений без дебаггеров
    }
    
    /**
     * Обновление аналитики оценок
     * 
     * @param array $grade_data
     * @return void
     */
    private static function update_grade_analytics(array $grade_data): void
    {
        // TODO: Реализуйте обновление аналитических данных
        // Например: обновление dashboard'ов, генерация отчетов
        
        // Логика обновления аналитики без дебаггеров
    }
    
    /**
     * Обработка интеграции с 1С
     * 
     * @param array $grade_data
     * @return void
     */
    private static function handle_1c_integration(array $grade_data): void
    {
        // Проверяем включена ли интеграция с 1С
        if (!grade_interceptor_config::is_1c_sync_enabled()) {
            return;
        }
        
        try {
            // Отправляем данные в 1С
            $success = onec_integration::send_grade_to_1c($grade_data);
            
            // Логика интеграции с 1С без дебаггеров
        } catch (Exception $e) {
            // Логируем ошибку без вывода в дебаггер
        }
    }
    
    /**
     * Хук перед обработкой обновления grade item
     * 
     * @param array $grade_data
     * @return void
     */
    public static function before_grade_item_processing(array $grade_data): void
    {
        // TODO: Логика перед обработкой grade item
        // Логика без дебаггеров
    }
    
    /**
     * Обработка обновления grade item
     * 
     * @param array $grade_data
     * @return void
     */
    public static function process_grade_item_update(array $grade_data): void
    {
        // TODO: Логика обработки grade item
        // Логика без дебаггеров
    }
    
    /**
     * Хук после обработки grade item
     * 
     * @param array $grade_data
     * @return void
     */
    public static function after_grade_item_processing(array $grade_data): void
    {
        // TODO: Логика после обработки grade item
        // Логика без дебаггеров
    }
    
    /**
     * Хук перед обработкой оценки submission
     * 
     * @param array $grade_data
     * @return void
     */
    public static function before_submission_grade_processing(array $grade_data): void
    {
        // TODO: Логика перед обработкой submission grade
        // Логика без дебаггеров
    }
    
    /**
     * Обработка оценки submission
     * 
     * @param array $grade_data
     * @return void
     */
    public static function process_submission_grade(array $grade_data): void
    {
        // TODO: Логика обработки submission grade
        // Логика без дебаггеров
    }
    
    /**
     * Хук после обработки оценки submission
     * 
     * @param array $grade_data
     * @return void
     */
    public static function after_submission_grade_processing(array $grade_data): void
    {
        // TODO: Логика после обработки submission grade
        // Логика без дебаггеров
    }
}

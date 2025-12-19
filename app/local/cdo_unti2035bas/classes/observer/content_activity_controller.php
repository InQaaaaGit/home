<?php
namespace local_cdo_unti2035bas\observer;

use core\event\base;
use local_cdo_unti2035bas\infrastructure\moodle\user_field_service;

/**
 * Обсервер контента активности
 * Отслеживает просмотр текстовых элементов, презентаций и других учебных материалов
 */
class content_activity_controller {

    /**
     * Обработчик просмотра контента
     */
    public static function handle_view(base $event): void {
        //self::handle_content_event($event, 'viewed');
    }

    /**
     * Обработчик скачивания контента
     */
    public static function handle_download(base $event): void {
        self::handle_content_event($event, 'downloaded');
    }

    /**
     * Общий обработчик событий контента
     */
    private static function handle_content_event(base $event, string $verb): void {
        global $USER;
        
        // Проверяем что это не системный пользователь
        if (!$USER || $USER->id < 2) {
            return;
        }

        // Проверяем что есть courseid
        if (!$event->courseid || $event->courseid < 2) {
            return;
        }

        try {
            $dependencies = new dependencies();
            
            // Проверяем, что курс привязан к потоку (имеет flow_id)
            $streamRepo = $dependencies->get_stream_repo();
            $streams = $streamRepo->read_by_courseid($event->courseid);
            if (empty($streams)) {
                debugging("cdo_unti2035bas: Курс {$event->courseid} не привязан к потоку, пропускаем обработку события контента", DEBUG_DEVELOPER);
                return;
            }
            
            $untiMappingService = $dependencies->get_unti_mapping_service();
            $usecase = $dependencies->get_content_activity_use_case();
            
            // Получаем данные для xAPI statement используя новый сервис
            $untiId = user_field_service::get_unti_id($USER->id);
            $contentId = self::generate_content_id($event);
            $untiCourseId = $untiMappingService->get_unti_course_id($event->courseid);
            $untiFlowId = $untiMappingService->get_unti_flow_id($event->courseid);
            //$moduleNumber = $untiMappingService->get_module_number_from_event($event);
            
            // Отправляем событие в зависимости от типа
            if ($verb === 'downloaded') {
                $usecase->handle_download(
                    $untiId,
                    $contentId,
                    $untiCourseId,
                    $untiFlowId,
                    1
                );
            } else {
                $usecase->handle_view(
                    $untiId,
                    $contentId,
                    $untiCourseId,
                    $untiFlowId,
                    1
                );
            }

        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            debugging('Content activity xAPI error: ' . $e->getMessage() . $e->text, DEBUG_DEVELOPER);
        }
    }

    /**
     * @deprecated Используйте user_field_service::get_unti_id() вместо этого
     */
    protected static function get_unti_id(int $userid): int {
        return user_field_service::get_unti_id($userid);
    }

    /**
     * Генерирует ID контента на основе события
     */
    private static function generate_content_id(base $event): string {
        // Формируем UUID для контента на основе события
        return sprintf(
            '%s-%s-%s',
            $event->component,
            $event->objecttable ?: 'unknown',
            $event->objectid ?: 0
        );
    }

    /**
     * Обработчик просмотра ресурсов
     */
    public static function resource_viewed(base $event): void {
        self::handle_view($event);
    }

    /**
     * Обработчик просмотра страниц
     */
    public static function page_viewed(base $event): void {
        self::handle_view($event);
    }

    /**
     * Обработчик скачивания файлов
     */
    public static function file_downloaded(base $event): void {
        self::handle_download($event);
    }
} 
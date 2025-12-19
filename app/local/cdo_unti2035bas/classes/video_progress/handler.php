<?php

namespace local_cdo_unti2035bas\video_progress;

use local_cdo_unti2035bas\application\statement\video_activity_use_case;
use local_cdo_unti2035bas\infrastructure\dependencies_base;
use local_cdo_unti2035bas\infrastructure\moodle\unti_mapping_service;
use local_cdo_unti2035bas\infrastructure\moodle\user_field_service;

class handler
{
    private dependencies_base $dependencies;

    public function __construct()
    {
        $this->dependencies = dependencies_base::get_instance();
    }

    public function get_sending_information(?int $flow_id = null): array
    {
        global $DB;
        
        $sql = "SELECT lv.id, ua.lrid, ua.position, lv.progress, lv.userid, cm.course, lv.cmid, lv.duration
                FROM {cdo_unti2035bas_activity} ua
                INNER JOIN {local_videoprogress} lv ON ua.modid = lv.cmid
                INNER JOIN {course_modules} cm ON cm.id = ua.modid";
        
        $params = [];
        
        // Если указан flow_id, добавляем фильтрацию через связанные таблицы
        if (!is_null($flow_id)) {
            $sql .= " INNER JOIN {cdo_unti2035bas_theme} t ON t.id = ua.themeid
                      INNER JOIN {cdo_unti2035bas_module} m ON m.id = t.moduleid
                      INNER JOIN {cdo_unti2035bas_block} b ON b.id = m.blockid
                      INNER JOIN {cdo_unti2035bas_stream} s ON s.id = b.streamid
                      WHERE s.untiflowid = ?";
            $params[] = $flow_id;
        }
        
        $result = $DB->get_records_sql($sql, $params);
        return $result;
    }

    /**
     * Отправляет xAPI данные о просмотре видео для записей прогресса
     * 
     * @param int|null $flow_id Если указан, отправляет только для активностей из этого потока
     */
    public function send_video_progress_statements(?int $flow_id = null): array
    {
        $videoProgress = $this->get_sending_information($flow_id);

        if (empty($videoProgress)) {
            debugging('No video progress data found for sending xAPI statements', DEBUG_DEVELOPER);
            return [
                'sent' => 0,
                'skipped' => 0,
                'errors' => 0,
                'total' => 0
            ];
        }

        $videoUseCase = $this->dependencies->get_video_activity_use_case();
        $untiMappingService = $this->dependencies->get_unti_mapping_service();
        $xapi_sent_repo = $this->dependencies->get_xapi_sent_repository();

        $sent_count = 0;
        $skipped_count = 0;
        $error_count = 0;

        foreach ($videoProgress as $progress) {
            try {
                if (isset($progress->lrid)) {
                    // Получаем cmid из данных активности
                    $cmid = $progress->cmid;

                    // Проверяем, не отправляли ли уже для этого пользователя/модуля/контента
//                if ($xapi_sent_repo->is_statement_sent($progress->userid, $cmid, $progress->lrid)) {
//                    debugging("cdo_unti2035bas: Statement уже был отправлен для user:{$progress->userid}, cmid:{$cmid}, lrid:{$progress->lrid}");
//                    $skipped_count++;
//                    continue;
//                }

                    $result = $this->send_single_video_progress_statement(
                        $progress,
                        $videoUseCase,
                        $untiMappingService
                    );

                    if (!empty($result)) {
                        // Сохраняем успешную отправку
                      /*  $xapi_sent_repo->save_sent_statement(
                            $progress->userid,
                            $cmid,
                            $progress->lrid,
                            $result[0],
                            $progress->progress,
                            $progress->duration
                        );*/
                        $sent_count++;
                        debugging("cdo_unti2035bas: Successfully sent and saved statement for user:{$progress->userid}, lrid:{$progress->lrid}");
                    } else {
                        $error_count++;
                        debugging("cdo_unti2035bas: Ошибка отправки statement для user:{$progress->userid}, lrid:{$progress->lrid}");
                    }
                } else {
                    $skipped_count++;
                }
            } catch (\Exception $e) {
                $error_count++;
                debugging("cdo_unti2035bas: Exception при отправке для user:{$progress->userid}, lrid:{$progress->lrid}: " . $e->getMessage());
            }
        }

        return [
            'sent' => $sent_count,
            'skipped' => $skipped_count,
            'errors' => $error_count,
            'total' => count($videoProgress)
        ];
    }

    /**
     * Отправляет xAPI statement для одной записи прогресса просмотра
     * @throws \Exception
     */
    public function send_single_video_progress_statement(
        $progress,
        video_activity_use_case $videoUseCase,
        unti_mapping_service $untiMappingService
    ): ?array
    {
        global $DB;

        // Получаем UNTI ID пользователя
        $untiId = user_field_service::get_unti_id($progress->userid);
        if (empty($untiId)) {
            debugging("UNTI ID not found for user {$progress->userid}", DEBUG_DEVELOPER);
            return null;
        }

        // Получаем правильные UNTI ID через mapping service
        $untiCourseId = $untiMappingService->get_unti_course_id($progress->course);
        $untiFlowId = $untiMappingService->get_unti_flow_id($progress->course);

        // Получаем номер модуля через course_flow_module_data
        $moduleNumber = $progress->position;
        
        // Вычисляем просмотренные секунды с округлением для избежания проблем с точностью
        $watch_seconds = round($progress->duration * ($progress->progress / 100));
        
        // Преобразуем в ISO формат
        $duration = video_activity_use_case::seconds_to_duration($watch_seconds);

        // Интовый процент просмотра видео
        $viewPercentage = (int)round($progress->progress);

        // Используем lrid как contentId (идентификатор контента методиста)
        $contentId = $progress->lrid;

        // Определяем, был ли это просмотр записи
        $watchedRecording = $this->is_recorded_video($progress->cmid);

        // Получаем URL записи если есть
        $recordingData = $this->get_recording_data($progress->cmid);

        // Отправляем xAPI statement
        return $videoUseCase->send_video_watch_statement(
            (string)$untiId,
            $contentId,
            $duration,
            $viewPercentage,
            $untiCourseId,
            $untiFlowId,
            $moduleNumber,
            $watchedRecording,
            $recordingData['url'] ?? null,
            $recordingData['size'] ?? null,
            $recordingData['hash'] ?? null
        );
    }

    /**
     * Получает общую длительность видео в секундах
     */
    private function get_video_total_duration(int $cmid): int
    {
        global $DB;

        // Пытаемся получить длительность из таблицы videoprogress
        $duration = $DB->get_field_sql(
            "SELECT MAX(duration) FROM mdl_local_videoprogress WHERE cmid = ?",
            [$cmid]
        );

        if ($duration && $duration > 0) {
            return (int)$duration;
        }

        // Если не найдено, возвращаем значение по умолчанию (например, 1 час)
        debugging("Video duration not found for cmid {$cmid}, using default 3600 seconds", DEBUG_DEVELOPER);
        return 3600;
    }

    /**
     * Вычисляет количество просмотренных секунд
     */
    private function calculate_watched_seconds(float $progress, int $totalDuration): int
    {
        // Если progress больше 1, считаем что это уже секунды
        if ($progress > 1) {
            return (int)$progress;
        }

        // Иначе считаем что это процент (0-1)
        return (int)($progress * $totalDuration);
    }

    /**
     * Определяет, является ли видео записью
     */
    private function is_recorded_video(int $cmid): bool
    {
        global $DB;

        // Проверяем в настройках модуля или по типу активности
        $moduleInfo = $DB->get_record_sql(
            "SELECT cm.*, m.name as modname 
             FROM mdl_course_modules cm 
             JOIN mdl_modules m ON cm.module = m.id 
             WHERE cm.id = ?",
            [$cmid]
        );

        if (!$moduleInfo) {
            return false;
        }

        // Логика определения записи (может быть расширена)
        // Например, если в названии есть "запись" или настройка модуля
        return $this->check_if_recording_by_module_settings($moduleInfo);
    }

    /**
     * Проверяет настройки модуля для определения записи
     */
    private function check_if_recording_by_module_settings($moduleInfo): bool
    {
        // Здесь может быть логика проверки настроек конкретного модуля
        // Например, для mod_url или mod_page с видео

        // Заглушка - можно расширить в зависимости от типа модуля
        return false;
    }

    /**
     * Получает данные о записи видео
     */
    private function get_recording_data(int $cmid): array
    {
        global $DB;

        // Здесь может быть логика получения URL, размера и хеша файла записи
        // из таблиц files, или настроек модуля

        $recordingData = [];

        // Пример получения файла из таблицы files
        $file = $DB->get_record_sql(
            "SELECT f.* 
             FROM mdl_files f
             JOIN mdl_context ctx ON f.contextid = ctx.id
             JOIN mdl_course_modules cm ON ctx.instanceid = cm.id
             WHERE cm.id = ? AND f.filename != '.' AND f.mimetype LIKE 'video/%'
             ORDER BY f.timemodified DESC
             LIMIT 1",
            [$cmid]
        );

        if ($file) {
            // Формируем URL файла
            $recordingData['url'] = $this->get_file_url($file);
            $recordingData['size'] = $file->filesize;
            $recordingData['hash'] = $file->contenthash;
        }

        return $recordingData;
    }

    /**
     * Формирует URL файла
     */
    private function get_file_url($file): string
    {
        global $CFG;

        if (!$file) {
            return '';
        }

        // Формируем стандартный URL Moodle файла
        return $CFG->wwwroot . '/pluginfile.php/' . $file->contextid . '/' .
            $file->component . '/' . $file->filearea . '/' . $file->itemid . '/' .
            $file->filename;
    }

    /**
     * Отправляет xAPI данные для конкретного пользователя и модуля
     * 
     * @param int $userId ID пользователя
     * @param int $cmid ID модуля курса
     * @return array Результат отправки
     */
    public function send_video_progress_for_user_module(int $userId, int $cmid): array
    {
        global $DB;
        
        // Получаем следы прогресса для конкретного пользователя и модуля
        $sql = "SELECT lv.id, ua.lrid, ua.position, lv.progress, lv.userid, cm.course, lv.cmid, lv.duration
                FROM {local_videoprogress} lv
                INNER JOIN {cdo_unti2035bas_activity} ua ON ua.modid = lv.cmid
                INNER JOIN {course_modules} cm ON cm.id = lv.cmid
                WHERE lv.userid = ? AND lv.cmid = ?";
        
        $videoProgress = $DB->get_records_sql($sql, [$userId, $cmid]);

        if (empty($videoProgress)) {
            debugging("No video progress data found for user {$userId}, cmid {$cmid}", DEBUG_DEVELOPER);
            return [
                'sent' => 0,
                'skipped' => 0,
                'errors' => 0,
                'total' => 0,
                'message' => 'Нет данных прогресса для отправки'
            ];
        }

        $videoUseCase = $this->dependencies->get_video_activity_use_case();
        $untiMappingService = $this->dependencies->get_unti_mapping_service();

        $sent_count = 0;
        $skipped_count = 0;
        $error_count = 0;
        $errors = [];

        foreach ($videoProgress as $progress) {
            try {
                if (isset($progress->lrid)) {
                    debugging("Sending statement for user {$userId}, cmid {$cmid}, lrid {$progress->lrid}", DEBUG_DEVELOPER);
                    
                    $result = $this->send_single_video_progress_statement(
                        $progress,
                        $videoUseCase,
                        $untiMappingService
                    );

                    if (!empty($result)) {
                        $sent_count++;
                        debugging("Successfully sent statement for user {$userId}, cmid {$cmid}, lrid {$progress->lrid}", DEBUG_DEVELOPER);
                    } else {
                        $skipped_count++;
                        debugging("Failed to send statement for user {$userId}, cmid {$cmid}, lrid {$progress->lrid}", DEBUG_DEVELOPER);
                    }
                } else {
                    $skipped_count++;
                    debugging("No lrid found for progress record {$progress->id}", DEBUG_DEVELOPER);
                }
            } catch (\Exception $e) {
                $error_count++;
                $errors[] = "Ошибка для записи {$progress->id}: " . $e->getMessage();
                debugging("Error sending video progress statement for user {$userId}, cmid {$cmid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        return [
            'sent' => $sent_count,
            'skipped' => $skipped_count,
            'errors' => $error_count,
            'total' => count($videoProgress),
            'error_details' => $errors
        ];
    }

    /**
     * Отправляет xAPI данные для всех следов конкретного пользователя
     * 
     * @param int $userId ID пользователя
     * @param int|null $flowId ID потока (опционально)
     * @return array Результат отправки
     */
    public function send_video_progress_for_user(int $userId, ?int $flowId = null): array
    {
        global $DB;
        
        $sql = "SELECT lv.id, ua.lrid, ua.position, lv.progress, lv.userid, cm.course, lv.cmid, lv.duration
                FROM {local_videoprogress} lv
                INNER JOIN {cdo_unti2035bas_activity} ua ON ua.modid = lv.cmid
                INNER JOIN {course_modules} cm ON cm.id = lv.cmid";
        
        $params = [$userId];
        
        // Если указан flow_id, добавляем фильтрацию
        if (!is_null($flowId)) {
            $sql .= " INNER JOIN {cdo_unti2035bas_theme} t ON t.id = ua.themeid
                      INNER JOIN {cdo_unti2035bas_module} m ON m.id = t.moduleid
                      INNER JOIN {cdo_unti2035bas_block} b ON b.id = m.blockid
                      INNER JOIN {cdo_unti2035bas_stream} s ON s.id = b.streamid
                      WHERE lv.userid = ? AND s.untiflowid = ?";
            $params[] = $flowId;
        } else {
            $sql .= " WHERE lv.userid = ?";
        }
        
        $videoProgress = $DB->get_records_sql($sql, $params);

        if (empty($videoProgress)) {
            debugging("No video progress data found for user {$userId}" . ($flowId ? ", flow {$flowId}" : ""), DEBUG_DEVELOPER);
            return [
                'sent' => 0,
                'skipped' => 0,
                'errors' => 0,
                'total' => 0,
                'message' => 'Нет данных прогресса для отправки'
            ];
        }

        $videoUseCase = $this->dependencies->get_video_activity_use_case();
        $untiMappingService = $this->dependencies->get_unti_mapping_service();

        $sent_count = 0;
        $skipped_count = 0;
        $error_count = 0;
        $errors = [];

        foreach ($videoProgress as $progress) {
            try {
                if (isset($progress->lrid)) {
                    $result = $this->send_single_video_progress_statement(
                        $progress,
                        $videoUseCase,
                        $untiMappingService
                    );

                    if (!empty($result)) {
                        $sent_count++;
                    } else {
                        $skipped_count++;
                    }
                } else {
                    $skipped_count++;
                    debugging("No lrid found for progress record {$progress->id}", DEBUG_DEVELOPER);
                }
            } catch (\Exception $e) {
                $error_count++;
                $errors[] = "Ошибка для записи {$progress->id}: " . $e->getMessage();
                debugging("Error sending video progress statement for user {$userId}: " . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        return [
            'sent' => $sent_count,
            'skipped' => $skipped_count,
            'errors' => $error_count,
            'total' => count($videoProgress),
            'error_details' => $errors
        ];
    }

    /**
     * Получить course module id по lrid
     */
    private function get_course_module_by_lrid($lrid)
    {
        global $DB;

        // Предполагаем, что есть связь между lrid и course module
        // Это может потребовать корректировки в зависимости от структуры БД
        $sql = "SELECT cm.id 
                FROM {course_modules} cm 
                JOIN {cdo_unti2035bas_activity} a ON a.cmid = cm.id 
                WHERE a.lrid = ?";

        $record = $DB->get_record_sql($sql, [$lrid]);
        return $record ? $record->id : null;
    }

    /**
     * Получить информацию о курсе по cmid
     */
    private function get_course_info_by_cmid($cmid)
    {
        global $DB;

        $sql = "SELECT c.id as courseid, cm.module, cm.instance
                FROM {course_modules} cm 
                JOIN {course} c ON c.id = cm.course 
                WHERE cm.id = ?";

        $record = $DB->get_record_sql($sql, [$cmid]);
        if ($record) {
            return [
                'courseid' => $record->courseid,
                'flow_id' => $record->courseid, // Можно адаптировать под нужную логику
                'module_number' => $record->instance
            ];
        }

        return ['courseid' => 0, 'flow_id' => 0, 'module_number' => 0];
    }
}
<?php

namespace local_cdo_unti2035bas\infrastructure\moodle;

use local_cdo_unti2035bas\infrastructure\persistence\module_repository;
use local_cdo_unti2035bas\infrastructure\persistence\stream_repository;

defined('MOODLE_INTERNAL') || die();

/**
 * Сервис для работы с данными по курсам и модулям УНТИ
 */
class unti_mapping_service
{
    private stream_repository $streamRepo;
    private module_repository $moduleRepo;

    public function __construct(stream_repository $streamRepo, module_repository $moduleRepo) {
        $this->streamRepo = $streamRepo;
        $this->moduleRepo = $moduleRepo;
    }

    /**
     * Получить длительность видео в секундах
     *
     * @param int $cmid ID модуля курса
     * @return int Длительность в секундах (по умолчанию 3600)
     */
    public function get_video_duration_seconds(int $cmid): int
    {
        global $DB;

        try {
            $sql = "SELECT COALESCE(v.duration, 3600) as duration
                    FROM {course_modules} cm
                    LEFT JOIN {videoplayer} v ON v.id = cm.instance AND cm.module = (
                        SELECT id FROM {modules} WHERE name = 'videoplayer'
                    )
                    WHERE cm.id = :cmid";

            $record = $DB->get_record_sql($sql, ['cmid' => $cmid]);

            if ($record && $record->duration > 0) {
                return (int)$record->duration;
            }

            return 3600; // По умолчанию 1 час

        } catch (\Exception $e) {
            error_log("Ошибка получения длительности видео для cmid {$cmid}: " . $e->getMessage());
            return 3600;
        }
    }

    /**
     * Проверить является ли видео записью трансляции
     *
     * @param int $cmid ID модуля курса
     * @return bool True если является записью трансляции
     */
    public function is_recorded_broadcast(int $cmid): bool
    {
        global $DB;

        try {
            $sql = "SELECT CASE 
                           WHEN v.is_recording = 1 OR v.broadcast_id IS NOT NULL THEN 1
                           ELSE 0
                           END as is_recording
                    FROM {course_modules} cm
                    LEFT JOIN {videoplayer} v ON v.id = cm.instance AND cm.module = (
                        SELECT id FROM {modules} WHERE name = 'videoplayer'
                    )
                    WHERE cm.id = :cmid";

            $record = $DB->get_record_sql($sql, ['cmid' => $cmid]);

            return $record && $record->is_recording == 1;

        } catch (\Exception $e) {
            error_log("Ошибка проверки записи трансляции для cmid {$cmid}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Получить данные о записи трансляции
     *
     * @param int $cmid ID модуля курса
     * @return array|null Данные о записи или null если не найдено
     */
    public function get_recording_data(int $cmid): ?array
    {
        global $DB;

        try {
            $sql = "SELECT v.broadcast_id,
                           v.recording_url,
                           v.recording_file_size,
                           v.recording_hash,
                           v.recording_start_time,
                           v.recording_end_time
                    FROM {course_modules} cm
                    LEFT JOIN {videoplayer} v ON v.id = cm.instance AND cm.module = (
                        SELECT id FROM {modules} WHERE name = 'videoplayer'
                    )
                    WHERE cm.id = :cmid AND (v.is_recording = 1 OR v.broadcast_id IS NOT NULL)";

            $record = $DB->get_record_sql($sql, ['cmid' => $cmid]);

            if ($record) {
                return [
                    'broadcast_id' => $record->broadcast_id,
                    'recording_url' => $record->recording_url,
                    'file_size' => (int)$record->recording_file_size,
                    'hash' => $record->recording_hash,
                    'start_time' => $record->recording_start_time,
                    'end_time' => $record->recording_end_time
                ];
            }

            return null;

        } catch (\Exception $e) {
            error_log("Ошибка получения данных записи для cmid {$cmid}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Получает UNTI course ID (program ID) по Moodle course ID
     *
     * @param int $courseid ID курса в Moodle
     * @return int UNTI program ID или course ID как fallback
     */
    public function get_unti_course_id(int $courseid): int {
        try {
            // Получаем streams для данного курса
            $streams = $this->streamRepo->read_by_courseid($courseid);

            if (!empty($streams)) {
                // Берем первый активный stream
                $stream = reset($streams);
                if ($stream && !$stream->deleted) {
                    debugging("Найден UNTI program ID {$stream->unti->programid} для курса {$courseid}", DEBUG_DEVELOPER);
                    return $stream->unti->programid;
                }
            }

            // Fallback: если stream не найден, возвращаем courseid
            debugging("UNTI stream не найден для курса {$courseid}, используем courseid как fallback", DEBUG_DEVELOPER);
            return $courseid;

        } catch (\Exception $e) {
            debugging("Ошибка получения UNTI course ID для курса {$courseid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            return $courseid;
        }
    }

    /**
     * Получает UNTI flow ID по Moodle course ID
     *
     * @param int $courseid ID курса в Moodle
     * @return int UNTI flow ID или course ID как fallback
     */
    public function get_unti_flow_id(int $courseid): int {
        try {
            // Получаем streams для данного курса
            $streams = $this->streamRepo->read_by_courseid($courseid);

            if (!empty($streams)) {
                // Берем первый активный stream
                $stream = reset($streams);
                if ($stream && !$stream->deleted) {
                    debugging("Найден UNTI flow ID {$stream->unti->flowid} для курса {$courseid}", DEBUG_DEVELOPER);
                    return $stream->unti->flowid;
                }
            }

            // Fallback: если stream не найден, возвращаем courseid
            debugging("UNTI stream не найден для курса {$courseid}, используем courseid как flow ID fallback", DEBUG_DEVELOPER);
            return $courseid;

        } catch (\Exception $e) {
            debugging("Ошибка получения UNTI flow ID для курса {$courseid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            return $courseid;
        }
    }

    /**
     * Получает номер модуля из события Moodle
     *
     * @param object $event Событие Moodle
     * @return int Номер модуля или 1 как fallback
     */
    public function get_module_number_from_event($event): int {
        // Пытаемся извлечь cmid из различных полей события
        $cmid = null;

        if (property_exists($event, 'contextinstanceid') && $event->contextinstanceid) {
            $cmid = $event->contextinstanceid;
        } elseif (property_exists($event, 'objectid') && $event->objectid) {
            $cmid = $event->objectid;
        } elseif (isset($event->other['cmid'])) {
            $cmid = $event->other['cmid'];
        }

        if ($cmid) {
            $courseData = $this->get_course_flow_module_data($cmid);
            return $courseData ? $courseData['module_number'] : 1;
        }

        // Если cmid не найден, возвращаем 1
        debugging("Module number could not be determined from event, using default value 1", DEBUG_DEVELOPER);
        return 1;
    }

    public function get_assessment_info(int $grade_item_id, int $untiflowid, $block_type='theoretical'): array
    {
        global $DB;
        $sql = 'SELECT gi.id,
                   gi.grademax,
                   gi.grademin,
                   gi.gradepass,
                   basa.lrid,
                   basa.parentobject,
                   basa.parentobjectid,
                   cm.instance,
                   cm.course,
                   cm.module,
                   cm.section,
                   cm.id cmid,
 				   basb.id,
				   basb.type_,
				   basm.blockid,
                   basm.position,
				   basm.id as basmid,
				   bass.untiflowid
    FROM {course_modules} cm 
        INNER JOIN {cdo_unti2035bas_assessment} basa ON basa.modid = cm.id 
        INNER JOIN {grade_items} gi ON gi.courseid = cm.course AND cm.instance = gi.iteminstance
        INNER JOIN {cdo_unti2035bas_block} basb ON basb.streamid = basa.parentobjectid OR basb.id = basa.parentobjectid
        INNER JOIN {cdo_unti2035bas_module} basm ON basm.blockid = basb.id
		INNER JOIN {cdo_unti2035bas_stream} bass ON bass.id = basb.streamid
	WHERE gi.id = ? AND basb.type_ = ? AND bass.untiflowid=?
	';
        $records = array_values($DB->get_records_sql($sql, [$grade_item_id, $block_type, $untiflowid]));

        // Конвертируем gradepass в проценты
        foreach ($records as $record) {
            if ($record->grademax > 0 && $record->gradepass !== null) {
                $record->gradepass_percent = ($record->gradepass / $record->grademax) * 100;
            } else {
                $record->gradepass_percent = 60.0; // значение по умолчанию
            }
        }

        return $records;
    }

    /**
     * Получить время прохождения теста для конкретной попытки
     *
     * @param int $cmid ID модуля курса
     * @param string $module_type Тип модуля (quiz, assign, lesson)
     * @param int $userid ID пользователя
     * @param int $attempt_number Номер попытки (по умолчанию последняя)
     * @return int|null Время прохождения в секундах или null если не найдено
     */
    public function get_test_duration(int $cmid, string $module_type, int $userid, int $attempt_number = null): ?int
    {
        global $DB;

        try {
            // Получаем информацию о модуле
            $cm = $DB->get_record('course_modules', ['id' => $cmid], 'instance', MUST_EXIST);

            // Определяем тип модуля и получаем время прохождения
            switch ($module_type) {
                case '18':
                case 'quiz':
                    return $this->get_quiz_duration($cm->instance, $userid, $attempt_number);
                case '1':
                case 'assign':
                    return $this->get_assign_duration($cm->instance, $userid, $attempt_number);
                case '15':
                case 'lesson':
                    return $this->get_lesson_duration($cm->instance, $userid, $attempt_number);

                default:
                    debugging("Module type '{$module_type}' not supported for duration calculation", DEBUG_DEVELOPER);
                    return null;
            }

        } catch (\Exception $e) {
            debugging("Error getting test duration: " . $e->getMessage(), DEBUG_DEVELOPER);
            return null;
        }
    }

    /**
     * Получить время прохождения quiz
     */
    private function get_quiz_duration(int $quiz_id, int $userid, int $attempt_number = null): ?int
    {
        global $DB;

        $sql = 'SELECT qa.id, qa.timefinish, qa.timestart, qa.attempt
                FROM {quiz_attempts} qa
                WHERE qa.quiz = ? AND qa.userid = ? AND qa.state = "finished"
                ORDER BY qa.attempt DESC';

        $attempts = $DB->get_records_sql($sql, [$quiz_id, $userid]);

        if (empty($attempts)) {
            return null;
        }

        // Если указан номер попытки, ищем её
        if ($attempt_number !== null) {
            foreach ($attempts as $attempt) {
                if ($attempt->attempt == $attempt_number) {
                    return $attempt->timefinish - $attempt->timestart;
                }
            }
            return null;
        }

        // Иначе берем последнюю попытку
        $last_attempt = reset($attempts);
        return $last_attempt->timefinish - $last_attempt->timestart;
    }

    /**
     * Получить время прохождения assignment
     */
    private function get_assign_duration(int $assign_id, int $userid, int $attempt_number = null): ?int
    {
        global $DB;

        $sql = 'SELECT asub.id, asub.timemodified, asub.timecreated
                FROM {assign_submission} asub
                WHERE asub.assignment = ? AND asub.userid = ? AND asub.status = "submitted"
                ORDER BY asub.timemodified DESC';

        $submissions = $DB->get_records_sql($sql, [$assign_id, $userid]);

        if (empty($submissions)) {
            return null;
        }

        // Для assignment используем время от создания до последнего изменения
        $last_submission = reset($submissions);
        return $last_submission->timemodified - $last_submission->timecreated;
    }

    /**
     * Получить время прохождения lesson
     */
    private function get_lesson_duration(int $lesson_id, int $userid, int $attempt_number = null): ?int
    {
        global $DB;

        $sql = 'SELECT lt.id, lt.completed, lt.starttime
                FROM {lesson_timer} lt
                WHERE lt.lessonid = ? AND lt.userid = ?
                ORDER BY lt.completed DESC';

        $timers = $DB->get_records_sql($sql, [$lesson_id, $userid]);

        if (empty($timers)) {
            return null;
        }

        // Берем последний таймер
        $last_timer = reset($timers);
        return $last_timer->completed - $last_timer->starttime;
    }
} 
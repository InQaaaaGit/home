<?php

namespace local_cdo_ag_tools\observers;

use coding_exception;
use dml_exception;
use Exception;
use local_cdo_ag_tools\services\work_notification_service;
use mod_assign\event\assessable_submitted;
use mod_assign\event\submission_created;
use mod_assign\event\submission_updated;
use mod_assign\event\submission_graded;
use moodle_exception;

/**
 * Observer для отслеживания событий работы с заданиями
 *
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class work_notification_observer
{
    /**
     * Обработчик события создания submission
     *
     * @param submission_created $event
     * @return void
     */
    public static function observe_submission_created(submission_created $event): void
    {
        try {
            self::handle_submission_upload($event);
        } catch (Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            debugging('Error in work_notification_observer::observe_submission_created: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Обработчик события обновления submission (когда студент загружает файлы)
     *
     * @param submission_updated $event
     * @return void
     */
    public static function observe_submission_updated(submission_updated $event): void
    {
        try {
            self::handle_submission_upload($event);
        } catch (Exception $e) {
            debugging('Error in work_notification_observer::observe_submission_updated: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Обработчик события assessable_submitted (финальная отправка работы)
     *
     * @param assessable_submitted $event
     * @return void
     */
    public static function observe_assessable_submitted(assessable_submitted $event): void
    {
        try {
            self::handle_submission_upload($event);
        } catch (Exception $e) {
            debugging('Error in work_notification_observer::observe_assessable_submitted: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Обработчик события оценивания письменной работы
     *
     * @param submission_graded $event
     * @return void
     */
    public static function observe_work_graded(submission_graded $event): void
    {
        try {
            self::handle_work_grading($event);
        } catch (Exception $e) {
            debugging('Error in work_notification_observer::observe_work_graded: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Обрабатывает загрузку работы и отправляет уведомление
     *
     * @param object $event Событие submission
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    private static function handle_submission_upload($event): void
    {
        global $DB;

        $eventData = $event->get_data();
        $userId = $eventData['userid'];
        $courseId = $eventData['courseid'];
        $assignmentId = $eventData['other']['assignmentid'] ?? null;
        $submissionId = $eventData['objectid'];

        if (!$assignmentId) {
            return;
        }

        // Проверяем, что это письменная работа (assignment)
        $assignment = $DB->get_record('assign', ['id' => $assignmentId]);
        if (!$assignment) {
            return;
        }

        // Проверяем, что submission действительно содержит файлы (это письменная работа)
        if (!self::submission_has_files($submissionId)) {
            return; // Не отправляем уведомление, если нет файлов
        }

        // Проверяем, не было ли уже отправлено уведомление для этой работы
        $existingNotification = $DB->get_record('local_cdo_ag_work_notif', [
            'userid' => $userId,
            'submissionid' => $submissionId,
        ]);

        if ($existingNotification && $existingNotification->upload_notified) {
            return; // Уведомление уже было отправлено
        }

        // Отправляем уведомление о загрузке
        $sent = work_notification_service::send_work_uploaded_notification(
            $userId,
            $courseId,
            $assignment->name
        );

        if ($sent) {
            // Сохраняем информацию о загруженной работе
            work_notification_service::log_work_upload(
                $userId,
                $courseId,
                $assignmentId,
                $submissionId
            );
        }
    }

    /**
     * Обрабатывает выставление оценки за письменную работу
     *
     * @param submission_graded $event
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    private static function handle_work_grading(submission_graded $event): void
    {
        global $DB;

        $eventData = $event->get_data();
        $userId = $eventData['relateduserid'];
        $courseId = $eventData['courseid'];
        $submissionId = $eventData['objectid'];
        $assignmentId = $eventData['other']['assignmentid'] ?? null;

        if (!$assignmentId) {
            return;
        }

        // Получаем информацию о задании
        $assignment = $DB->get_record('assign', ['id' => $assignmentId]);
        if (!$assignment) {
            return;
        }

        // Проверяем, что это письменная работа с файлами
        if (!self::submission_has_files($submissionId)) {
            return;
        }

        // Проверяем, не было ли уже отправлено уведомление о проверке
        if (work_notification_service::is_graded_notification_sent($userId, $submissionId)) {
            return;
        }

        // Получаем оценку
        $sql = "SELECT gg.finalgrade
                FROM {grade_grades} gg
                JOIN {grade_items} gi ON gi.id = gg.itemid
                WHERE gi.itemtype = 'mod'
                  AND gi.itemmodule = 'assign'
                  AND gi.iteminstance = :assignmentid
                  AND gg.userid = :userid";

        $params = [
            'assignmentid' => $assignmentId,
            'userid' => $userId,
        ];

        $gradeRecord = $DB->get_record_sql($sql, $params);
        
        if (!$gradeRecord || $gradeRecord->finalgrade === null) {
            return; // Оценка еще не выставлена
        }

        // Отправляем уведомление о проверке работы
        $sent = work_notification_service::send_work_graded_notification(
            $userId,
            $courseId,
            $assignment->name,
            $gradeRecord->finalgrade
        );

        if ($sent) {
            // Отмечаем, что уведомление о проверке отправлено
            work_notification_service::mark_work_as_graded_notified($userId, $submissionId);
        }
    }

    /**
     * Проверяет, содержит ли submission файлы
     *
     * @param int $submissionId ID submission
     * @return bool
     * @throws dml_exception
     */
    private static function submission_has_files(int $submissionId): bool
    {
        global $DB;

        // Проверяем наличие файлов в submission
        $sql = "SELECT COUNT(f.id)
                FROM {files} f
                JOIN {assign_submission} asub ON asub.id = :submissionid
                JOIN {context} ctx ON ctx.instanceid = asub.assignment
                WHERE ctx.contextlevel = :contextlevel
                  AND f.contextid = ctx.id
                  AND f.component = 'assignsubmission_file'
                  AND f.filearea = 'submission_files'
                  AND f.itemid = :itemid
                  AND f.filename != '.'";

        $params = [
            'submissionid' => $submissionId,
            'contextlevel' => CONTEXT_MODULE,
            'itemid' => $submissionId,
        ];

        $count = $DB->count_records_sql($sql, $params);
        
        return $count > 0;
    }
}


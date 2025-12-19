<?php

namespace local_cdo_ag_tools\controllers;

use dml_exception;
use moodle_exception;
use stdClass;

class grade_notification_controller {
    /**
     * Создает новую запись об уведомлении
     *
     * @param int $userid ID пользователя
     * @param int $courseid ID курса
     * @param float $grade Оценка
     * @param string $modulename Наименование модуля
     * @param string $moduletype Тип модуля
     * @return int ID созданной записи
     * @throws dml_exception
     */
    public static function create_notification(
        int $userid,
        int $courseid,
        float $grade,
        string $modulename,
        string $moduletype
    ): int {
        global $DB;

        $record = new stdClass();
        $record->userid = $userid;
        $record->courseid = $courseid;
        $record->grade = $grade;
        $record->modulename = $modulename;
        $record->moduletype = $moduletype;
        $record->timecreated = time();

        return $DB->insert_record('local_cdo_ag_grade_notifications', $record);
    }

    /**
     * Получает все уведомления для пользователя
     *
     * @param int $userid ID пользователя
     * @return array Массив уведомлений
     * @throws dml_exception
     */
    public static function get_user_notifications(int $userid): array {
        global $DB;

        return $DB->get_records(
            'local_cdo_ag_grade_notifications',
            ['userid' => $userid],
            'timecreated DESC'
        );
    }

    /**
     * Получает все уведомления для курса
     *
     * @param int $courseid ID курса
     * @return array Массив уведомлений
     * @throws dml_exception
     */
    public static function get_course_notifications(int $courseid): array {
        global $DB;

        return $DB->get_records(
            'local_cdo_ag_grade_notifications',
            ['courseid' => $courseid],
            'timecreated DESC'
        );
    }

    /**
     * Получает уведомление по ID
     *
     * @param int $id ID уведомления
     * @return stdClass|null Объект уведомления или null, если не найдено
     * @throws dml_exception
     */
    public static function get_notification(int $id): ?stdClass {
        global $DB;

        return $DB->get_record('local_cdo_ag_grade_notifications', ['id' => $id]);
    }

    /**
     * Удаляет уведомление
     *
     * @param int $id ID уведомления
     * @return bool Результат удаления
     * @throws dml_exception
     */
    public static function delete_notification(int $id): bool {
        global $DB;

        return $DB->delete_records('local_cdo_ag_grade_notifications', ['id' => $id]);
    }

    /**
     * Обновляет уведомление
     *
     * @param int $id ID уведомления
     * @param array $data Данные для обновления
     * @return bool Результат обновления
     * @throws dml_exception
     */
    public static function update_notification(int $id, array $data): bool {
        global $DB;

        $record = new stdClass();
        $record->id = $id;
        foreach ($data as $key => $value) {
            $record->$key = $value;
        }

        return $DB->update_record('local_cdo_ag_grade_notifications', $record);
    }

    /**
     * Получает статистику по уведомлениям для пользователя
     *
     * @param int $userid ID пользователя
     * @return stdClass Объект со статистикой
     * @throws dml_exception
     */
    public static function get_user_statistics(int $userid): stdClass {
        global $DB;

        $stats = new stdClass();
        $stats->total_notifications = $DB->count_records(
            'local_cdo_ag_grade_notifications',
            ['userid' => $userid]
        );
        $stats->average_grade = $DB->get_field_sql(
            "SELECT AVG(grade) FROM {local_cdo_ag_grade_notifications} WHERE userid = ?",
            [$userid]
        );
        $stats->last_notification = $DB->get_record(
            'local_cdo_ag_grade_notifications',
            ['userid' => $userid],
            '*',
            'timecreated DESC'
        );

        return $stats;
    }

    /**
     * Получает уведомления для пользователя с пагинацией
     *
     * @param int $userid ID пользователя
     * @param int $limit Количество записей
     * @param int $offset Смещение
     * @return array Массив уведомлений
     * @throws dml_exception
     */
    public static function get_user_notifications_paginated(
        int $userid,
        int $limit = 10,
        int $offset = 0
    ): array {
        global $DB;

        return $DB->get_records(
            'local_cdo_ag_grade_notifications',
            ['userid' => $userid],
            'timecreated DESC',
            '*',
            $offset,
            $limit
        );
    }

    /**
     * Получает уведомления для курса с пагинацией
     *
     * @param int $courseid ID курса
     * @param int $limit Количество записей
     * @param int $offset Смещение
     * @return array Массив уведомлений
     * @throws dml_exception
     */
    public static function get_course_notifications_paginated(
        int $courseid,
        int $limit = 10,
        int $offset = 0
    ): array {
        global $DB;

        return $DB->get_records(
            'local_cdo_ag_grade_notifications',
            ['courseid' => $courseid],
            'timecreated DESC',
            '*',
            $offset,
            $limit
        );
    }

    /**
     * Получает последние уведомления с лимитом
     *
     * @param int $limit Количество записей
     * @return array Массив уведомлений
     * @throws dml_exception
     */
    public static function get_latest_notifications(int $limit = 10): array {
        global $DB;

        return $DB->get_records(
            'local_cdo_ag_grade_notifications',
            null,
            'timecreated ASC',
            '*',
            0,
            $limit
        );
    }

    /**
     * Получает количество уведомлений для пользователя
     *
     * @param int $userid ID пользователя
     * @return int Количество уведомлений
     * @throws dml_exception
     */
    public static function count_user_notifications(int $userid): int {
        global $DB;

        return $DB->count_records(
            'local_cdo_ag_grade_notifications',
            ['userid' => $userid]
        );
    }

    /**
     * Получает количество уведомлений для курса
     *
     * @param int $courseid ID курса
     * @return int Количество уведомлений
     * @throws dml_exception
     */
    public static function count_course_notifications(int $courseid): int {
        global $DB;

        return $DB->count_records(
            'local_cdo_ag_grade_notifications',
            ['courseid' => $courseid]
        );
    }
} 
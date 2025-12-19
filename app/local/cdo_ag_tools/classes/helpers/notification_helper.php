<?php

namespace local_cdo_ag_tools\helpers;

use coding_exception;
use core_user;
use dml_exception;
use moodle_exception;
use local_cdo_ag_tools\factories\grade_notification_factory;

class notification_helper
{
    /**
     * Отправляет email-уведомление пользователю
     *
     * @param int $userid ID пользователя
     * @param string $subject Тема письма
     * @param string $message_text
     * @param string $component Компонент системы (по умолчанию 'local_cdo_ag_tools')
     * @param string $eventtype Тип события (по умолчанию 'grade_update')
     * @return bool Результат отправки
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function send_email_notification(
        int $userid,
        string $subject,
        string $message_text,
        string $component = 'local_cdo_ag_tools',
        string $eventtype = 'grade_update'
    ): bool {
        global $CFG;

        // Получаем данные пользователя
        $user = core_user::get_user($userid);
        if (!$user) {
            throw new moodle_exception('usernotfound', 'local_cdo_ag_tools');
        }

        // Проверяем, что у пользователя есть email
        if (empty($user->email)) {
            throw new moodle_exception('noemail', 'local_cdo_ag_tools');
        }

        // Создаем объект сообщения
        $message = new \core\message\message();
        $message->component = $component;
        $message->name = $eventtype;
        $message->userfrom = core_user::get_noreply_user();
        $message->userto = $user;
        $message->subject = $subject;
        $message->fullmessage = $message_text;
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = '<p>' . $message_text . '</p>';
        $message->smallmessage = $message_text;
        $message->notification = 1;
        $message->contexturl = (new \moodle_url('/course/'))->out(false);
        $message->contexturlname = get_string('course_list', 'local_cdo_ag_tools');

        // Отправляем сообщение
        return message_send($message);
    }

    /**
     * Отправляет уведомление о новой оценке
     *
     * @param int $userid ID пользователя
     * @param string $coursename Название курса
     * @param float $grade Оценка
     * @param string $activityname Название активности
     * @param string $modtype Тип элемента курса (например, 'assign', 'quiz', 'lesson')
     * @return bool Результат отправки
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function send_grade_notification(
        int $userid,
        string $coursename,
        float $grade,
        string $activityname,
        string $modtype
    ): bool {
        $params = [
            'coursename' => $coursename,
            'grade' => $grade,
            'activityname' => $activityname,
            'fullname' => fullname(core_user::get_user($userid))
        ];

        $handler = grade_notification_factory::create_handler($modtype);

        return self::send_email_notification(
            $userid,
            $handler->get_subject(),
            $handler->get_message($params),
            'local_cdo_ag_tools',
            'grade_update'
        );
    }
}

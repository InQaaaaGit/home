<?php

namespace local_cdo_ag_tools\tasks;

use coding_exception;
use dml_exception;
use local_cdo_ag_tools\controllers\grade_notification_controller;
use local_cdo_ag_tools\helpers\notification_helper;
use moodle_exception;

class sender_notifications_task extends \core\task\scheduled_task
{

    public function get_name(): \lang_string|string
    {
        return get_string('sender_notifications', 'local_cdo_ag_tools');
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function execute(): void
    {
        mtrace('Запуск отправки 499 уведомлений об оценках');
        $notifies_to_send = grade_notification_controller::get_latest_notifications(499);
        foreach ($notifies_to_send as $notify_to_send) {
            $course = get_course($notify_to_send->courseid);
            notification_helper::send_grade_notification(
                $notify_to_send->userid,
                $course->fullname,
                $notify_to_send->grade,
                $notify_to_send->modulename,
                $notify_to_send->moduletype
            );
            grade_notification_controller::delete_notification($notify_to_send->id);
        }
        mtrace('Успешно');
    }
}
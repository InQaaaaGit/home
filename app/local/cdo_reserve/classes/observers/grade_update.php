<?php

namespace local_cdo_reserve\observers;

use core\event\user_graded;
use local_cdo_ag_tools\controllers\grade_doubling;
use local_cdo_ag_tools\helpers\notification_helper;
use moodle_exception;
class grade_update
{
    /**
     * @throws moodle_exception
     */
    public static function observe_grade_updated(user_graded $event): void
    {
        $event_data = $event->get_data();
        /*$course_id = $event_data['courseid'];
        $related_user_id = $event_data['relateduserid'];
        $course = get_course($course_id);
        // Обновляем оценки с учетом удвоения
        grade_doubling::realise_doubling($related_user_id, $course_id);

        // Получаем информацию о grade_item
        $grade_item = grade_item::fetch(['id' => $event_data['other']['itemid']]);
        if (!$grade_item) {
            //throw new moodle_exception('gradeitemnotfound', 'local_cdo_ag_tools');
            //return;
        }

        if ($grade_item->itemtype !== 'category' && $grade_item->itemtype !== 'course') {
            // Отправляем уведомление

             grade_notification_controller::create_notification(
                $related_user_id, // userid
                $course_id, // courseid
                $event_data['other']['finalgrade'], // grade
                $grade_item->get_name(), // modulename
                $grade_item->itemmodule // moduletype
            );
            notification_helper::send_grade_notification(
                $related_user_id,
                $course->fullname,
                $event_data['other']['finalgrade'],
                $grade_item->get_name(),
                $grade_item->itemmodule
            );
        }*/
    }
}
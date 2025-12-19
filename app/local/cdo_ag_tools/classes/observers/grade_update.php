<?php

namespace local_cdo_ag_tools\observers;

use core\event\user_graded;
use Exception;
use grade_item;
use local_cdo_ag_tools\controllers\grade_doubling;
use local_cdo_ag_tools\controllers\grade_notification_controller;
use local_cdo_ag_tools\grades\grades_with_double_coefficient;
use local_cdo_ag_tools\helpers\notification_helper;
use local_cdo_ag_tools\helpers\grade_data_helper;
use local_cdo_ag_tools\factories\grade_strategy_factory;
use moodle_exception;

class grade_update
{
    /**
     * @throws moodle_exception
     */
    public static function observe_grade_updated(user_graded $event): void
    {
        $event_data = $event->get_data();
        $course_id = $event_data['courseid'];
        $related_user_id = $event_data['relateduserid'];
        $course = get_course($course_id);
        // Обновляем оценки с учетом удвоения
        $grade_item = grade_item::fetch(['id' => $event_data['other']['itemid']]);
        if (!$grade_item) {
            return;
        }
        /*$s = grade_doubling::get_cmid($grade_item->iteminstance);
        $double_cm = (new grades_with_double_coefficient)->get_customfield_data_double_coefficient((int)$s->id);
        if ($double_cm)*/
        grade_doubling::realise_doubling($related_user_id, $course_id);

        // Получаем информацию о grade_item




        if ($grade_item->itemtype !== 'category' && $grade_item->itemtype !== 'course'
            && $event_data['other']['finalgrade'] != null) {
            // Отправляем уведомление

            grade_notification_controller::create_notification(
                $related_user_id, // userid
                $course_id, // courseid
                $event_data['other']['finalgrade'], // grade
                $grade_item->get_name(), // modulename
                $grade_item->itemmodule // moduletype
            );
            /* notification_helper::send_grade_notification(
                 $related_user_id,
                 $course->fullname,
                 $event_data['other']['finalgrade'],
                 $grade_item->get_name(),
                 $grade_item->itemmodule
             );*/
        }
    }
}
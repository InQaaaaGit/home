<?php

namespace local_cdo_ag_tools\controllers;

use local_cdo_ag_tools\grades\grades_with_double_coefficient;
use moodle_exception;
use stdClass;

class grade_doubling
{
    /**
     * @throws moodle_exception
     */
    public static function realise_doubling($related_user_id, $course_id): void
    {
        $user_std = new stdClass();
        $user_std->id = $related_user_id;
        (new grades_with_double_coefficient())->update_grade_for_single_user(
            $user_std,
            $course_id
        );
    }

    public static function get_cmid($iteminstance) {
        global $DB;
        return $DB->get_record("course_modules", array("instance" => $iteminstance));
    }
}
<?php

namespace local_cdo_ag_tools\helpers;

use context_course;
use core_user;
use dml_exception;
use gradereport_user\external\user;

class get_grade_items_helper extends user
{
    /**
     * @throws dml_exception
     */
    public static function get_grade_items_helper(int $courseid, int $userid = 0, int $groupid = 0): array
    {
        $course = get_course($courseid);
        $context = context_course::instance($course->id);
        $user = core_user::get_user($userid, '*', MUST_EXIST);
        list($gradeitems, $warnings) = user::get_report_data($course, $context, $user, $userid, $groupid, false);
        foreach ($gradeitems as $gradeitem) {
            if (isset($gradeitem['feedback']) && isset($gradeitem['feedbackformat'])) {
                list($gradeitem['feedback'], $gradeitem['feedbackformat']) =
                    \core_external\util::format_text($gradeitem['feedback'], $gradeitem['feedbackformat'], $context->id);
            }
        }

        return [
            'usergrades' => $gradeitems,
            'warnings' => $warnings
        ];
    }
}

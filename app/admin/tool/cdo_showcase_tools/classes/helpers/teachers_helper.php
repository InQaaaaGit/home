<?php

namespace tool_cdo_showcase_tools\helpers;

use context_course;
use dml_exception;

class teachers_helper
{
    /**
     * @throws dml_exception
     */
    public static function get_teachers_on_course($course_id): array
    {
        $course_context = context_course::instance($course_id);
        global $DB;
        $SQL = 'SELECT *
                FROM mdl_role_assignments ra
                         INNER JOIN mdl_user u ON ra.userid = u.id
                WHERE ra.roleid = ?
                  AND ra.contextid = ?';
        $result = $DB->get_records_sql($SQL, [3, $course_context->id]);
        return array_values($result);
    }
}
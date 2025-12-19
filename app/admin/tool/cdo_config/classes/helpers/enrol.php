<?php

namespace tool_cdo_config\helpers;

use dml_exception;

class enrol
{
    /**
     * @throws dml_exception
     */
    public static function get_enrol_timings($userid, $courseid)
    {
        global $DB;
        $records = $DB->get_records_sql("SELECT ue.timestart, ue.timeend
                                       FROM {user_enrolments} ue
                                          INNER JOIN {enrol} e ON e.id = ue.enrolid
                                       WHERE ue.userid=? AND e.enrol = 'manual' AND e.courseid=?",
            [$userid, $courseid]
        );
        $return = [];
        foreach ($records as $record) {
            $return['timestart'] = $record->timestart;
            $return['timeend'] = $record->timeend;
        }
        return $return;
    }
}
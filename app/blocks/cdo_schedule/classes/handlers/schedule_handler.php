<?php

namespace block_cdo_schedule\handlers;

use stdClass;
use tool_cdo_config\di;

class schedule_handler
{
    public static function get_full_schedule_data($course_id = '', $group_id = '', $start_date = '', $end_date = ''): array
    {

        $options = di::get_instance()->get_request_options();
        $options->set_properties(
            [
                'date_end' => $end_date,
                'date_start' => $start_date,
                'group_id' => $group_id,
                'course_id' => $course_id
            ]
        );

        $request = di::get_instance()->get_request('get_full_schedule')->request($options);
        $data = $request->get_request_result()->to_array();

        return $data;
    }

    public static function get_user_schedule_data($type='student', $period='month'): array
    {
        global $USER;
        $options = di::get_instance()->get_request_options();
        $options->set_properties(
            [
                'date' => date('Ymd'),
                'period' => $period,
                'user_id' => $USER->id,
                'type' => $type
            ]
        );

        $request = di::get_instance()->get_request('get_schedule')->request($options);
        $data = $request->get_request_result()->to_array();

        return $data;
    }
}
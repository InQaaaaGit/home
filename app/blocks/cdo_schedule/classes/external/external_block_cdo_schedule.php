<?php

namespace block_cdo_schedule\external;

use block_cdo_schedule\handlers\schedule_handler;
use coding_exception;
use core\output\html_writer;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

class external_block_cdo_schedule extends external_api
{
    public static function get_schedule_data_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'type' => new external_value(PARAM_TEXT, 'type', VALUE_DEFAULT, ''),
                'course_id' => new external_value(PARAM_TEXT, 'ID курса', VALUE_DEFAULT, ''),
                'group_id' => new external_value(PARAM_TEXT, 'ID группы', VALUE_DEFAULT, ''),
                'start_date' => new external_value(PARAM_TEXT, 'Дата начала', VALUE_DEFAULT, ''),
                'end_date' => new external_value(PARAM_TEXT, 'Дата окончания', VALUE_DEFAULT, ''),
            ]
        );
    }

    /**
     */
    public static function get_schedule_data($type = '', $course_id = '', $group_id = '', $start_date = '', $end_date = ''): array
    {
        // Для публичного доступа используем прямой вызов schedule_handler
        $data_return = schedule_handler::get_full_schedule_data($course_id, $group_id, $start_date, $end_date);
        
        $prepared_data = [];
        foreach ($data_return as $schedule_student_item) {
            foreach ($schedule_student_item['items'] as $schedule_student_item_items) {
                $last_string = '';
                $need_array = $schedule_student_item_items['teachers'];
                $added_information = $schedule_student_item_items['subgroups']['name'];

                foreach ($need_array as $item) {
                    $last_string = $item['name'];
                }

                $text_body =  '<b>' . $schedule_student_item_items['lesson']['discipline']['name'] . '</b><br>';
                if (!empty($schedule_student_item_items['classrooms']['building']['name'])) {
                    $text_body .= $schedule_student_item_items['classrooms']['building']['name'] . '<br>';
                }
                if (!empty($schedule_student_item_items['classrooms']['room']['name'])) {
                    $text_body .= $schedule_student_item_items['classrooms']['room']['name'] . '<br>';
                }
                if (!empty($schedule_student_item_items['lesson']['lesson_type']['name'])) {
                    $text_body .= $schedule_student_item_items['lesson']['lesson_type']['name'] . '<br>';
                }
                if (!empty($last_string)) {
                    $text_body .= $last_string . '<br>';
                }
                if (!empty($added_information)) {
                    $text_body .= $added_information;
                }

                // Генерируем уникальный ID для события
                $unique_id = uniqid('event_', true);
                
                $prepared_data[] = [
                    'id' => $unique_id,
                    'start_date' => date('Y-m-d', strtotime($schedule_student_item_items['date'])) . ' ' . $schedule_student_item_items['start_time'],
                    'end_date' => date('Y-m-d', strtotime($schedule_student_item_items['date'])) . ' ' . $schedule_student_item_items['end_time'],
                    'text' => $text_body,
                    'room' => $schedule_student_item_items['classrooms']['room']['name'],
                    'address' => $schedule_student_item_items['classrooms']['building']['name'],
                    'teacher' => $last_string,
                ];
            }
        }
        
        return $prepared_data;
    }

    public static function get_schedule_data_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                    'text' => new external_value(PARAM_RAW, '', VALUE_REQUIRED),
                    'start_date' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                    'end_date' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                    'room' => new external_value(PARAM_TEXT, '', VALUE_DEFAULT, ''),
                    'address' => new external_value(PARAM_TEXT, '', VALUE_DEFAULT, ''),
                    'teacher' => new external_value(PARAM_TEXT, '', VALUE_DEFAULT, ''),
                ]
            )
        );
    }

    public static function get_set_attendance_parameters(): external_function_parameters
    {
        global $USER;
        return new external_function_parameters(
            [
                'date1c' => new external_value(PARAM_TEXT, 'The date in 1C format'),
                'discipline' => new external_value(PARAM_TEXT, 'The discipline'),
                'edu_plan' => new external_value(PARAM_TEXT, 'The education plan'),
                'lesson_type' => new external_value(PARAM_TEXT, 'The lesson type'),
                'period_of_study' => new external_value(PARAM_TEXT, 'The period of study'),
                'group' => new external_value(PARAM_TEXT, 'The period of study'),
                'training_course' => new external_value(PARAM_TEXT, 'The period of study'),
                'employee' => new external_value(PARAM_TEXT, '$employee'),
                'time_start' => new external_value(PARAM_TEXT, 'time_start'),
                'time_end' => new external_value(PARAM_TEXT, 'time_end'),
                'user_id' => new external_value(
                    PARAM_TEXT, 'The period of study', VALUE_DEFAULT, $USER->id),
            ]
        );
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws cdo_config_exception
     * @throws invalid_parameter_exception
     */
    public static function get_set_attendance($date1c, $discipline,
                                              $edu_plan, $lesson_type,
                                              $period_of_study, $group,
                                              $training_course, $employee,
                                              $time_start, $time_end): array
    {
        // Parameter validation.
        $params = self::validate_parameters(self::get_set_attendance_parameters(),
            [
                'date1c' => $date1c,
                'discipline' => $discipline,
                'edu_plan' => $edu_plan,
                'lesson_type' => $lesson_type,
                'period_of_study' => $period_of_study,
                'group' => $group,
                'training_course' => $training_course,
                'employee' => $employee,
                'time_start' => $time_start,
                'time_end' => $time_end,
            ]);
        $options = di::get_instance()->get_request_options();
        $options->set_properties(
            $params
        );
        $options->set_parameters_in_json();
        $request = di::get_instance()->get_request('get_set_attendance')->request($options);
        $data = $request->get_request_result()->to_array();

        return $data;
    }

    public static function get_set_attendance_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'error' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                'attendance_data' => new external_single_structure(
                    [
                        'date1c' => new external_value(PARAM_TEXT, 'The date in 1C format'),
                        'name' => new external_value(PARAM_TEXT, 'The date in 1C format'),
                        'discipline' => new external_value(PARAM_TEXT, 'The discipline'),
                        'lesson_type' => new external_value(PARAM_TEXT, 'The lesson type'),
                        'period_of_study' => new external_value(PARAM_TEXT, 'The period of study'),
                        'group' => new external_value(PARAM_TEXT, 'The period of study'),
                        'training_course' => new external_value(PARAM_TEXT, 'The period of study'),
                    ]
                ),
                'message' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                'guid_attendance' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                'attendance' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'student' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                            'student_fio' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                            'attendance_status' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                        ]
                    )
                )
            ]
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function set_attendance_parameters(): external_function_parameters
    {
        global $USER;
        return new external_function_parameters(
            [
                'GUIDSheet' => new external_value(PARAM_TEXT, 'GUID of the attendance sheet'),
                'GUIDStudent' => new external_value(PARAM_TEXT, 'GUID of the student'),
                'GUIDGrade' => new external_value(PARAM_TEXT, 'GUID of the grade/attendance status'),
                'user_id' => new external_value(
                    PARAM_TEXT, '', VALUE_DEFAULT, $USER->id),
            ]
        );
    }

    /**
     * Returns description of method result.
     *
     * @return external_single_structure
     */
    public static function set_attendance_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'success' => new external_value(PARAM_BOOL, 'Status of the operation (true = success)'),
                'error' => new external_value(PARAM_TEXT, 'Status of the operation (true = success)'),
            ]
        );
    }

    /**
     * Sets attendance data.
     *
     * @param string $GUIDSheet
     * @param string $GUIDStudent
     * @param string $GUIDGrade
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function set_attendance(string $GUIDSheet, string $GUIDStudent, string $GUIDGrade): array
    {
        $params = self::validate_parameters(self::set_attendance_parameters(),
            [
                'GUIDSheet' => $GUIDSheet,
                'GUIDStudent' => $GUIDStudent,
                'GUIDGrade' => $GUIDGrade,
            ]);
        $options = di::get_instance()->get_request_options();
        $options->set_properties(
            $params
        );
        $options->set_parameters_in_json();
        $request = di::get_instance()->get_request('set_grade')->request($options);
        $data = $request->get_request_result()->to_array();

        return $data;
    }

    public static function get_full_schedule_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'type' => new external_value(PARAM_TEXT, 'type', VALUE_DEFAULT, ''),
                'course_id' => new external_value(PARAM_TEXT, 'ID курса', VALUE_DEFAULT, ''),
                'group_id' => new external_value(PARAM_TEXT, 'ID группы', VALUE_DEFAULT, ''),
                'start_date' => new external_value(PARAM_TEXT, 'Дата начала', VALUE_DEFAULT, ''),
                'end_date' => new external_value(PARAM_TEXT, 'Дата окончания', VALUE_DEFAULT, ''),
            ]
        );
    }

    /**
     * Get full schedule data with detailed information
     *
     * @param string $type Type of schedule
     * @return array Schedule data
     */
    public static function get_full_schedule(string $type = ''): array
    {
        // Parameter validation.
        $params = self::validate_parameters(self::get_full_schedule_parameters(),
            [
                'type' => $type,
            ]);
        
        $data_return = schedule_handler::get_user_schedule_data($params['type']);
        $prepared_data = [];
        foreach ($data_return as $schedule_student_item) {
            foreach ($schedule_student_item['items'] as $schedule_student_item_items) {
                $last_string = '';
                $need_array = $schedule_student_item_items['teachers'];
                $added_information = $schedule_student_item_items['subgroups']['name'];

                foreach ($need_array as $item) {
                    $last_string = $item['name'];
                }

                $text_body =  '<b>' . $schedule_student_item_items['lesson']['discipline']['name'] . '</b><br>';
                if (!empty($schedule_student_item_items['classrooms']['building']['name'])) {
                    $text_body .= $schedule_student_item_items['classrooms']['building']['name'] . '<br>';
                }
                if (!empty($schedule_student_item_items['classrooms']['room']['name'])) {
                    $text_body .= $schedule_student_item_items['classrooms']['room']['name'] . '<br>';
                }
                if (!empty($schedule_student_item_items['lesson']['lesson_type']['name'])) {
                    $text_body .= $schedule_student_item_items['lesson']['lesson_type']['name'] . '<br>';
                }
                if (!empty($last_string)) {
                    $text_body .= $last_string . '<br>';
                }
                if (!empty($added_information)) {
                    $text_body .= $added_information;
                }

                // Генерируем уникальный ID для события
                $unique_id = uniqid('event_', true);
                
                $prepared_data[] = [
                    'id' => $unique_id,
                    'start_date' => date('Y-m-d', strtotime($schedule_student_item_items['date'])) . ' ' . $schedule_student_item_items['start_time'],
                    'end_date' => date('Y-m-d', strtotime($schedule_student_item_items['date'])) . ' ' . $schedule_student_item_items['end_time'],
                    'text' => $text_body,
                    'room' => $schedule_student_item_items['classrooms']['room']['name'],
                    'address' => $schedule_student_item_items['classrooms']['building']['name'],
                    'teacher' => $last_string,
                ];
            }
        }
        
        // Отладочная информация
        error_log('get_full_schedule: подготовлено ' . count($prepared_data) . ' событий');
        if (!empty($prepared_data)) {
            error_log('get_full_schedule: первое событие: ' . json_encode($prepared_data[0]));
        }
        
        return $prepared_data;
    }

    public static function get_full_schedule_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                    'text' => new external_value(PARAM_RAW, '', VALUE_REQUIRED),
                    'start_date' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                    'end_date' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                    'room' => new external_value(PARAM_TEXT, '', VALUE_DEFAULT, ''),
                    'address' => new external_value(PARAM_TEXT, '', VALUE_DEFAULT, ''),
                    'teacher' => new external_value(PARAM_TEXT, '', VALUE_DEFAULT, ''),
                ]
            )
        );
    }


}

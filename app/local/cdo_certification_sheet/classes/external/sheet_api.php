<?php

namespace local_cdo_certification_sheet\external;

use coding_exception;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\response_dto;

class sheet_api extends external_api
{

    /**
     * @return external_function_parameters
     * @throws coding_exception
     */
    public static function update_grade_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'grade' => new external_value(
                    PARAM_TEXT,
                    get_string('sheet_api_guid_grade', 'local_cdo_certification_sheet')
                ),
                'student' => new external_value(
                    PARAM_TEXT,
                    get_string('sheet_api_guid_student', 'local_cdo_certification_sheet')
                ),
                'sheet' => new external_value(
                    PARAM_TEXT,
                    get_string('sheet_api_guid_sheet', 'local_cdo_certification_sheet')
                ),
                'theme' => new external_value(
                    PARAM_TEXT,
                    get_string('sheet_api_guid_sheet', 'local_cdo_certification_sheet'),
                    VALUE_DEFAULT,
                    ''
                ),
                'point_semester' => new external_value(
                    PARAM_RAW,
                    get_string('sheet_api_guid_sheet', 'local_cdo_certification_sheet'),
                    VALUE_DEFAULT,
                    0
                ),
                'point_control_event' => new external_value(
                    PARAM_INT,
                    get_string('sheet_api_guid_sheet', 'local_cdo_certification_sheet'),
                    VALUE_DEFAULT,
                    0
                ),
                'is_brs' => new external_value(
                    PARAM_TEXT,
                    "",
                    VALUE_DEFAULT,
                    false
                ),
                'rating_type' => new external_value(
                    PARAM_TEXT,
                    "",
                    VALUE_DEFAULT,
                    false
                ),
                'rating_value' => new external_value(
                    PARAM_INT,
                    "",
                    VALUE_DEFAULT,
                    0
                ),
                'note' => new external_value(
                    PARAM_TEXT,
                    get_string('sheet_api_note', 'local_cdo_certification_sheet'),
                    VALUE_DEFAULT,
                    ''
                )
            ],
            get_string('sheet_api_change_current_grade', 'local_cdo_certification_sheet')
        );
    }

    /**
     * @param string $grade
     * @param string $student
     * @param string $sheet
     * @param string $theme
     * @param mixed $point_semester
     * @param int $point_control_event
     * @param string $is_brs
     * @param string $rating_type
     * @param string $note
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public static function update_grade(string $grade,
                                        string $student,
                                        string $sheet,
                                        string $theme = "",
                                        int $point_semester = 0,
                                        int $point_control_event = 0,
                                        string $is_brs = "",
                                        string $rating_type = "",
                                        int $rating_value = 0,
                                        string $note = ""

    ): array
    {
        global $USER;
        //TODO изменить на проде на $USER->id
        $user_id = $USER->id;
       # $user_id = 3;
        $options = di::get_instance()->get_request_options();
        $options->set_properties([
            "GUIDGrade" => $grade,
            "GUIDSheet" => $sheet,
            "GUIDStudent" => $student,
            "user_id" => $user_id,
            "theme" => ($theme),
            "point_semester" => ($point_semester),
            "point_control_event" => ($point_control_event),
            "is_brs" => ($is_brs),
            "rating_type" => ($rating_type),
            "rating_value" => ($rating_value),
            "note" => ($note),
        ]);

        $result = di::get_instance()
            ->get_request($is_brs)
            ->request($options)
            ->get_request_result()
            ->to_array();

        if (!$result['success']) {
            throw new cdo_config_exception(0, $result['error'], true);
        }

        return $result;
    }

    public static function update_grade_returns(): external_single_structure
    {
        $rating_structure = new external_single_structure(
            [
                'grade' => new external_value(PARAM_INT, 'The numeric grade'),
                'GUIDGrade' => new external_value(PARAM_TEXT, 'GUID of the grade'),
            ]
        );

        $teacher_structure = new external_single_structure(
            [
                'FIO' => new external_value(PARAM_TEXT, 'Teacher full name'),
                'user_id' => new external_value(PARAM_TEXT, 'Teacher user ID'),
            ]
        );

        $grade_details_structure = new external_single_structure(
            [
                'adr' => $rating_structure,
                'ricd' => $rating_structure,
                'frd' => $rating_structure,
                'grade' => $rating_structure,
                'ysc' => $rating_structure,
                'teacher' => $teacher_structure,
            ]
        );

        return new external_single_structure(
            [
                'success' => new external_value(PARAM_BOOL, 'Success status'),
                'error' => new external_value(PARAM_TEXT, 'Error message', VALUE_OPTIONAL),
                'grade' => $grade_details_structure,
            ]
        );
    }

    /**
     * @return external_function_parameters
     * @throws coding_exception
     */
    public static function commission_agreed_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'grades' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'save_grade' => new external_value(
                                PARAM_TEXT,
                                get_string('sheet_api_guid_save_grade', 'local_cdo_certification_sheet')
                            ),
                            'sheet_guid' => new external_value(
                                PARAM_TEXT,
                                get_string('sheet_api_guid_sheet', 'local_cdo_certification_sheet')
                            ),
                            'student_guid' => new external_value(
                                PARAM_TEXT,
                                get_string('sheet_api_guid_student', 'local_cdo_certification_sheet')
                            ),
                        ],
                        get_string('sheet_api_structure_info_about_grade', 'local_cdo_certification_sheet')
                    ),
                    get_string('sheet_api_list_current_grades', 'local_cdo_certification_sheet')
                ),
                'sheet_guid' => new external_value(
                    PARAM_TEXT,
                    get_string('sheet_api_guid_sheet', 'local_cdo_certification_sheet')
                ),
                'user_id' => new external_value(
                    PARAM_TEXT,
                    get_string('sheet_api_guid_sheet', 'local_cdo_certification_sheet')
                ),

            ],
            get_string('sheet_api_agreed_sheet', 'local_cdo_certification_sheet')
        );
    }

    /**
     * @param array $grades
     * @param string $sheet_guid
     * @param string $user_id
     * @return array
     * @throws cdo_config_exception
     * @throws coding_exception|cdo_type_response_exception
     */
    public static function commission_agreed(array $grades, string $sheet_guid, string $user_id): array
    {

        $student_grades = [];

        foreach ($grades as $grade) {
            $student_grades[$grade['student_guid']] = $grade;
        }

        $options = di::get_instance()->get_request_options();
        //TODO изменить на проде на $USER->id
        $options->set_properties(["user_id" => $user_id]);
        $save_sheets = di::get_instance()
            ->get_request("get_list_sheet")
            ->request($options)
            ->get_request_result()
            ->to_array();

        $current_student_grades = [];

        foreach ($save_sheets as $save_sheet) {
            if ($save_sheet['guid'] === $sheet_guid) {
                $current_student_grades = $save_sheet['students'];
                break;
            }
        }

        $errors = [];

        if (!count($current_student_grades)) {
            $errors[] = get_string('sheet_api_sheet_is_empty', 'local_cdo_certification_sheet');
        }

        if (count($student_grades) !== count($current_student_grades)) {
            $errors[] = get_string('sheet_api_count_student_change', 'local_cdo_certification_sheet');
        }

        foreach ($current_student_grades as $student) {
            if (!array_key_exists($student['guid'], $student_grades)) {
                $errors[] = get_string('sheet_api_student_not_found', 'local_cdo_certification_sheet');
            }

            if ($student_grades[$student['guid']]['save_grade'] !== $student['grade']) {
                $errors[] = get_string('sheet_api_grades_not_match', 'local_cdo_certification_sheet');
            }
        }

        if (!count($errors)) {
            $options = di::get_instance()->get_request_options();
            $options->set_properties([
                'GUIDSheet' => $sheet_guid,
                'user_id' => $user_id,
                'agreed' => true,

            ]);
            $change_agreed = di::get_instance()
                ->get_request("change_agreed")
                ->request($options)
                ->get_request_result()
                ->to_array();

            if ($change_agreed['error'] !== "") {
                throw new cdo_config_exception(0, $change_agreed['error'], true);
            }
            return $change_agreed;
        }

        throw new cdo_config_exception(0, implode('\\n', $errors), true);
    }

    public static function commission_agreed_returns()
    {
        return null;
    }

    /**
     * @return external_function_parameters
     * @throws coding_exception
     */
    public static function close_sheet_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'sheet_guid' => new external_value(
                    PARAM_TEXT,
                    get_string('sheet_api_guid_sheet', 'local_cdo_certification_sheet')
                )
            ],
            get_string('sheet_api_structure_close_sheet', 'local_cdo_certification_sheet')
        );
    }

    /**
     * @param string $sheet_guid
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     */
    public static function close_sheet(string $sheet_guid): array
    {

        $options = di::get_instance()->get_request_options();
        $options->set_properties(["GUIDSheet" => $sheet_guid]);
        #_dump($options);
        $result = di::get_instance()
            ->get_request("close_sheet")
            ->request($options)
            ->get_request_result()
            ->to_array();

        if (!$result['success']) {
            throw new cdo_config_exception(3007);
        }

        return $result;
    }

    public static function close_sheet_returns()
    {
        return null;
    }

    public static function get_list_sheet_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [

            ]
        );
    }

    /**
     * @throws cdo_type_response_exception
     * @throws cdo_config_exception
     * @throws coding_exception
     */
    public static function get_list_sheet(): array
    {
        global $USER, $CFG;
        $options = di::get_instance()->get_request_options();
        //TODO изменить на проде на $USER->id
        $options->set_properties(["user_id" => $USER->id]);
        #$options->set_properties(["user_id" => 3]);

        //TODO
       /* $json = file_get_contents($CFG->dirroot.'/local/cdo_certification_sheet/scratch_1.json', true);
        $real_json = response_dto::transform(
            "local_cdo_certification_sheet\DTO\certification_sheet_dto",
            json_decode($json)
        );
        return $real_json->to_array();*/
        //--TODO
        return di::get_instance()
            ->get_request("get_list_sheet")
            ->request($options)
            ->get_request_result()
            ->to_array();
    }

    public static function get_list_sheet_returns()
    {
        return null;

    }

}

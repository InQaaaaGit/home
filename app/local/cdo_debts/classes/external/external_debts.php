<?php

namespace local_cdo_debts\external;

use coding_exception;
use curl;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use invalid_parameter_exception;

use local_cdo_debts\output\debts\controller_service;
use local_cdo_debts\output\debts\renderable;
use moodle_exception;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

defined('MOODLE_INTERNAL') || die();

class external_debts extends external_api
{
    public static function get_academic_debts_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            []
        );
    }

    public static function get_academic_debts(): array
    {
        $renderable = new renderable();
        return $renderable->get_academic_debts();
    }

    public static function get_academic_debts_returns()
    {
        return null;
    }

    public static function create_request_retake_parameters(): external_function_parameters
    {
        global $USER;
        return new external_function_parameters(
            [
                'who' => new external_value(PARAM_TEXT, 'binary_string', VALUE_REQUIRED),
                'body' => new external_value(PARAM_RAW, 'binary_string', VALUE_REQUIRED),
                'document_id' => new external_value(PARAM_TEXT, 'binary_string', VALUE_REQUIRED),
                'filebs64' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'binaryString' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ''),
                            'filename' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ''),
                            'type' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ''),
                        ]
                    ),
                    '',
                    VALUE_DEFAULT,
                    []
                ),
                'subject' => new external_value(PARAM_RAW, 'binary_string', VALUE_REQUIRED),
                'gradebook' => new external_value(PARAM_RAW, 'binary_string', VALUE_REQUIRED),
                'user_id' => new external_value(PARAM_INT, 'binary_string', VALUE_DEFAULT, $USER->id),
                'file' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ""),
                'date' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ""),
                'date_for_retake_convert' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ""),
                'status' => helper::directory_structure(),
                'date_for_retake' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ""),
                'discipline' => new external_value(PARAM_TEXT, 'binary_string', VALUE_DEFAULT, ""),
                'student' => new external_value(PARAM_TEXT, 'binary_string', VALUE_DEFAULT, ""),
                'student_id' => new external_value(PARAM_TEXT, 'binary_string', VALUE_DEFAULT, ""),
                'commentary' => new external_value(PARAM_TEXT, 'binary_string', VALUE_DEFAULT, ""),
                'document_source' => helper::directory_structure(),
                'teachers' => helper::directory_structure(),
            ]
        );
    }

    /**
     * @param $who
     * @param $body
     * @param $document_id
     * @param $filebs64
     * @param $subject
     * @param $gradebook
     * @return array
     * @throws invalid_parameter_exception
     */
    public static function create_request_retake($who, $body, $document_id, $filebs64, $subject, $gradebook): array
    {
        global $CFG;
        $params = self::validate_parameters(self::create_request_retake_parameters(),
            [
                'who' => $who,
                'body' => $body,
                'document_id' => $document_id,
                'filebs64' => $filebs64,
                'subject' => $subject,
                'gradebook' => $gradebook,
            ]
        );

        $renderable = new renderable();
        return $renderable->send_request_retake($who, $body, $document_id, $filebs64, $subject, $params['user_id'], $params['gradebook']);
    }

    public static function create_request_retake_returns()
    {
        return null;

    }

    public static function get_retake_list_by_user_id_parameters(): external_function_parameters
    {
        global $USER;
        return new external_function_parameters(
            [
                'user_id' => new external_value(PARAM_INT, 'user_id', VALUE_DEFAULT, $USER->id),
            ]
        );
    }

    /**
     * @throws invalid_parameter_exception
     */
    public static function get_retake_list_by_user_id($user_id = 0): array
    {
        $params = self::validate_parameters(self::get_retake_list_by_user_id_parameters(),
            [
                //   'user_id' => $user_id TODO
                'user_id' => 11738
            ]
        );
        $controller_service = new controller_service();
        return $controller_service->get_retake_list_by_user_id($params['user_id']);
    }

    public static function get_retake_list_by_user_id_returns()
    {
        return null;
    }

    public static function update_status_retake_parameters(): external_function_parameters
    {
        global $USER;
        return new external_function_parameters(
            [
                'document_id' => new external_value(PARAM_TEXT, '$document_id', VALUE_REQUIRED),
                'gradebook' => new external_value(PARAM_TEXT, 'user_id', VALUE_REQUIRED),
                'user_id' => new external_value(PARAM_TEXT, 'user_id', VALUE_DEFAULT, $USER->id),
                'commentary' => new external_value(PARAM_TEXT, 'commentary', VALUE_REQUIRED),
                'status' => new external_value(PARAM_INT, 'user_id', VALUE_DEFAULT, '0'),
                'date_for_retake' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ""),
            ]
        );
    }

    /**
     * @throws invalid_parameter_exception
     */
    public static function update_status_retake($document_id, $gradebook, $user_id, $commentary, $status, $date_for_retake): array
    {
        $params = self::validate_parameters(self::update_status_retake_parameters(),
            [
                'document_id' => $document_id,
                'gradebook' => $gradebook,
                'user_id' => $user_id,
                'status' => $status,
                'commentary' => $commentary,
                'date_for_retake' => $date_for_retake,
            ]
        );

        $controller_service = new controller_service();
        return $controller_service->update_status_request($params);
    }

    public static function update_status_retake_returns()
    {
        return null;
    }

}
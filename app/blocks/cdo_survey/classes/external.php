<?php

namespace block_cdo_survey;

defined('MOODLE_INTERNAL') || die();


use block_cdo_survey\helpers\external_helper;
use coding_exception;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use Exception;
use invalid_parameter_exception;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

use block_cdo_survey\DTO\education_level_dto;

class external extends external_api
{
    const PLUGIN_NAME = 'block_cdo_survey';

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function submit_survey_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'form_data' => external_helper::form_data_external()
            ]
        );
    }

    /**
     * Submits the survey data.
     *
     * @param array $form_data
     * @return array
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function submit_survey(array $form_data): array
    {
        global $USER;
        // Validate parameters.
        $params = self::validate_parameters(self::submit_survey_parameters(), ['form_data' => $form_data]);
        $params['firstname'] = $USER->firstname;
        $params['lastname'] = $USER->lastname;
        $params['middlename'] = $USER->middlename;
        $params['email'] = $USER->email;
        $params['username'] = $USER->username;
        $params['user_id'] = $USER->id;
        $options = di::get_instance()->get_request_options();
        $options->set_properties(
            $params
        );
        $options->set_parameters_in_json();
        $request = di::get_instance()->get_request('send_survey')->request($options);
        try {
            $data = $request->get_request_result()->to_array();
        } catch (coding_exception|cdo_config_exception|cdo_type_response_exception $e) {
            return [
                'status' => 0,
                'message' => $e->getMessage(),
            ];
        }

        return [
            'status' => 1,
            'message' => get_string(self::PLUGIN_NAME, 'submit_success'),
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function submit_survey_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'status' => new external_value(PARAM_INT, 'Status of the operation'),
                'message' => new external_value(PARAM_TEXT, 'Success or error message'),
            ]
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_citizenship_parameters(): external_function_parameters
    {
        return new external_function_parameters([]); // No input parameters
    }

    /**
     * Returns description of method result.
     *
     * @return external_multiple_structure
     */
    public static function get_citizenship_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure([
                'name' => new external_value(PARAM_TEXT, 'The name of the citizenship'),
                'value' => new external_value(PARAM_INT, 'The value of the citizenship'),
            ])
        );
    }

    /**
     * Returns citizenship data.
     *
     * @return array
     * @throws cdo_type_response_exception
     * @throws cdo_config_exception
     * @throws coding_exception
     */
    public static function get_citizenship(): array
    {

        $options = di::get_instance()->get_request_options();
        $options->set_properties([]); // No properties needed for this example.

        return di::get_instance()
            ->get_request('get_citizenship_data') // Placeholder service name.
            ->request($options)
            ->get_request_result()
            ->to_array();
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_education_levels_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            array() // No parameters needed for fetching the list
        );
    }

    /**
     * Returns education levels
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public static function get_education_levels(): array
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties([]); // No properties needed for this example.

        return di::get_instance()
            ->get_request('get_education_levels') // Placeholder service name.
            ->request($options)
            ->get_request_result()
            ->to_array();
    }

    /**
     * Returns description of method result value
     * @return external_multiple_structure
     */
    public static function get_education_levels_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'name' => new external_value(PARAM_TEXT, 'Education level name'),
                    'value' => new external_value(PARAM_INT, 'Education level value')
                )
            )
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_user_groups_parameters(): external_function_parameters
    {
        return new external_function_parameters([]); // No input parameters
    }

    /**
     * Returns description of method result.
     *
     * @return external_multiple_structure
     */
    public static function get_user_groups_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure([
                'name' => new external_value(PARAM_TEXT, 'The name of the group'),
                'value' => new external_value(PARAM_INT, 'The id of the group'),
            ])
        );
    }

    /**
     * Returns user group data.
     *
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public static function get_user_groups(): array
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties([]); // No properties needed for this example.

        return di::get_instance()
            ->get_request('get_user_groups') // Placeholder service name.
            ->request($options)
            ->get_request_result()
            ->to_array();
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_course_schedule_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [

            ]
        );
    }

    /**
     * Returns description of method result.
     *
     * @return external_multiple_structure
     */
    public static function get_course_schedule_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure([
                'courseID' => new external_value(PARAM_TEXT, 'ID of the course'),
                'scheduleData' => new external_multiple_structure(
                    new external_single_structure([
                            'scheduleGUID' => new external_value(PARAM_TEXT, 'GUID of the schedule entry'),
                            'nameSchedule' => new external_value(PARAM_TEXT, 'Name of the schedule entry'),
                            'dataStartSchedule' => new external_value(PARAM_TEXT, 'Start date of the schedule entry'),
                            'dataEndSchedule' => new external_value(PARAM_TEXT, 'End date of the schedule entry'),
                        ]
                    )
                )
            ]
        ));
    }

    /**
     * Returns course schedule data.
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public static function get_course_schedule(): array
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties([]); // No properties needed for this example.

        return di::get_instance()
            ->get_request('get_course_schedule') // Placeholder service name.
            ->request($options)
            ->get_request_result()
            ->to_array();
    }
     /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_identity_document_types_parameters(): external_function_parameters {
        return new external_function_parameters([]); // No input parameters
    }

    /**
     * Returns description of method result.
     *
     * @return external_multiple_structure
     */
    public static function get_identity_document_types_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'name' => new external_value(PARAM_TEXT, 'The name of the document type'),
                'value' => new external_value(PARAM_TEXT, 'The value of the document type'),
            ])
        );
    }

    /**
     * Returns identity document types data.
     *
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public static function get_identity_document_types(): array
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties([]); // No properties needed for this example.

        return di::get_instance()
            ->get_request('get_identity_document_types') // Placeholder service name.
            ->request($options)
            ->get_request_result()
            ->to_array();
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_survey_data_parameters(): external_function_parameters
    {
        return new external_function_parameters([]); // No input parameters needed as we'll use current user
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function get_survey_data_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'email' => new external_value(PARAM_TEXT, 'User email'),
                'lastname' => new external_value(PARAM_TEXT, 'User lastname'),
                'firstname' => new external_value(PARAM_TEXT, 'User firstname'),
                'form_data' => external_helper::form_data_external()
            ]
        );
    }

    /**
     * Gets survey data for the current user.
     *
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function get_survey_data(): array
    {
        global $USER;

        // Validate parameters.
        self::validate_parameters(self::get_survey_data_parameters(), []);

     //   try {
            $options = di::get_instance()->get_request_options();
            $options->set_properties([
                'user_id' => $USER->id
            ]);

            $request = di::get_instance()->get_request('get_survey_data')->request($options);
            $data = $request->get_request_result()->to_array();

            return $data;
      /*  } catch (Exception $e) {
            return [
                'status' => 0,
                'data' => null,
                'message' => $e->getMessage()
            ];
        }*/
    }
}

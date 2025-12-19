<?php

namespace local_cdo_rpd\services;

use block_cdo_professional_info\output\professional_info\renderable;
use coding_exception;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use invalid_parameter_exception;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

class print_rpd extends external_api
{
    public static function get_plan_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'rpd_id' => new external_value(PARAM_TEXT, 'rpd_id', VALUE_REQUIRED),
                'edu_plan' => new external_value(PARAM_TEXT, 'edu_plan', VALUE_REQUIRED),
                'user_id' => new external_value(PARAM_TEXT, 'edu_plan', VALUE_REQUIRED),
                'discipline' => new external_value(PARAM_TEXT, 'edu_plan', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws cdo_config_exception
     * @throws invalid_parameter_exception
     */
    public static function get_plan($rpd_id, $edu_plan, $user_id, $discipline): string
    {
        $params = self::validate_parameters(self::get_plan_parameters(),
            [
                'rpd_id' => $rpd_id,
                'edu_plan' => $edu_plan,
                'user_id' => $user_id,
                'discipline' => $discipline,
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_plan')->request($options);
        return $request->get_request_result(true)/*->to_array()*/ ;
    }

    public static function get_plan_returns()
    {

    }

    public static function get_list_plans_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'rpd_id' => new external_value(PARAM_TEXT, 'rpd_id', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws cdo_config_exception
     * @throws invalid_parameter_exception
     */
    public static function get_list_plans($rpd_id): \tool_cdo_config\request\DTO\response_dto|string
    {
        $params = self::validate_parameters(self::get_list_plans_parameters(),
            [
                'rpd_id' => $rpd_id,
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_list_plans')->request($options);
        return $request->get_request_result(true)/*->to_array()*/ ;
    }

    public static function get_list_plans_returns()
    {

    }

    public static function get_chair_info_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'chair_name' => new external_value(PARAM_TEXT, 'rpd_id', VALUE_DEFAULT, ''),
            ]
        );
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws cdo_config_exception
     * @throws invalid_parameter_exception
     */
    public static function get_chair_info($chair_name): array
    {
        $params = self::validate_parameters(self::get_chair_info_parameters(),
            [
                'chair_name' => $chair_name,
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_chair_info')->request($options);
        return $request->get_request_result()->to_array();
    }

    public static function get_chair_info_returns()
    {

    }

    public static function get_user_info_parameters(): external_function_parameters
    {
        global $USER;
        return new external_function_parameters(
            [
                'user_id' => new external_value(PARAM_TEXT, 'user_id', VALUE_DEFAULT, $USER->id),
            ]
        );
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws cdo_config_exception
     * @throws invalid_parameter_exception
     */
    public static function get_user_info($user_id): array
    {
        return (new renderable)->get_professional_info();
        /*$params = self::validate_parameters(self::get_user_info_parameters(),
            [
                'user_id' => $user_id,
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_user_info')->request($options);
        return $request->get_request_result()->to_array();*/
    }

    public static function get_user_info_returns()
    {

    }

    public static function get_rpd_info_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'edu_plan' => new external_value(PARAM_TEXT, 'edu_plan', VALUE_REQUIRED),
                'discipline' => new external_value(PARAM_TEXT, 'id discipline', VALUE_REQUIRED),
                'rpd_id' => new external_value(PARAM_TEXT, 'id discipline', VALUE_REQUIRED),
                'user_id' => new external_value(PARAM_TEXT, 'id discipline', VALUE_REQUIRED),
                'module_guid' => new external_value(PARAM_RAW, 'id discipline', VALUE_DEFAULT, "")
            ]
        );
    }

    /**
     * @param $edu_plan
     * @param $discipline
     * @param $rpd_id
     * @param $user_id
     * @param string|null $guid_module
     * @return mixed
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function get_rpd_info($edu_plan, $discipline, $rpd_id, $user_id, string|null $guid_module = ""): mixed
    {
        $params = self::validate_parameters(self::get_rpd_info_parameters(),
            [
                'edu_plan' => $edu_plan,
                'discipline' => $discipline,
                'rpd_id' => $rpd_id,
                'user_id' => $user_id,
                'module_guid' => $guid_module
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_competencies_for_rpd')->request($options);
        return $request->get_request_result($return_body = true);
    }

    public static function get_competencies_for_rpd_returns()
    {
    }
}
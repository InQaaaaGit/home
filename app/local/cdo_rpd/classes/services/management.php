<?php

namespace local_cdo_rpd\services;

use coding_exception;
use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use dml_exception;
use invalid_parameter_exception;
use local_cdo_rpd\helpers\plugin;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

class management extends external_api
{
    public static function set_status_parameters(): external_function_parameters
    {
        global $USER;
        return new external_function_parameters (
            [
                'rpd_id' => new external_value(PARAM_TEXT, 'rpd_id', VALUE_REQUIRED),
                'status' => new external_value(PARAM_TEXT, '1 2 3', VALUE_REQUIRED),
                'user_id' => new external_value(PARAM_TEXT, 'user-id', VALUE_DEFAULT, $USER->id),
                'reason_disagreed' => new external_value(PARAM_TEXT, 'reason_disagreed', VALUE_DEFAULT, ""),
                'type' => new external_value(PARAM_TEXT, 'type', VALUE_DEFAULT, plugin::$cdo_work_with_rpd),
            ]
        );
    }

    /**
     * @param $rpd_id
     * @param $status
     * @param $user_id
     * @param $reason_disagreed
     * @param $type
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function set_status($rpd_id, $status, $user_id, $reason_disagreed, $type): array
    {
        $params = self::validate_parameters(self::set_status_parameters(),
            [
                'rpd_id' => $rpd_id,
                'status' => $status,
                'user_id' => $user_id,
                'reason_disagreed' => $reason_disagreed,
                'type' => $type,
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $options->set_parameters_in_json();
        $request = di::get_instance()->get_request('set_status')->request($options);
        $result = $request->get_request_result()->to_array();
        return $result;

    }

    public static function set_status_returns()
    {
        return null;
    }
}
<?php

namespace local_cdo_debts\output\debts;

use Throwable;
use tool_cdo_config\di;

class controller_service
{
    public function get_retake_list_by_user_id($user_id = 0): array
    {
        if ($user_id) {
            global $USER;
            $user_id = $USER->id;
        }
        try {
            $options = di::get_instance()->get_request_options();
            $options->set_properties(
                [
                    // "user_id" => $user_id, //TODO
                    "user_id" => "11738", //TODO
                ]
            )->set_parameters_in_json();
            $request = di::get_instance()->get_request('get_retake_list_by_user_id')->request($options);

            $data = $request->get_request_result()->to_array();
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage() . $e->getTraceAsString()
            ];
        }

        return $data;
    }

    public function update_status_request($params = []): array
    {
        try {
            $options = di::get_instance()->get_request_options();
            $options->set_properties(
                $params
            )->set_parameters_in_json();
            $request = di::get_instance()->get_request('update_status_request')->request($options);

            return $request->get_request_result()->to_array();
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage() . $e->getTraceAsString()
            ];
        }
    }
}
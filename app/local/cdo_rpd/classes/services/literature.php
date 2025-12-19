<?php

namespace local_cdo_rpd\services;

use coding_exception;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;
use local_cdo_rpd\helpers\external;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

class literature extends external_api
{
    public static function get_literature_for_approve_parameters(): external_function_parameters
    {
        global $USER;
        return new external_function_parameters(
            [
                'user_id' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, $USER->id),
                'rpd_id' => new external_value(PARAM_TEXT, 'id', VALUE_OPTIONAL),
                'guid' => new external_value(PARAM_TEXT, 'id', VALUE_OPTIONAL)
            ]
        );
    }

    /**
     * @param $user_id
     * @param $rpd_id
     * @param $guid
     * @return array
     * @throws coding_exception
     * @throws cdo_type_response_exception
     * @throws cdo_config_exception
     * @throws invalid_parameter_exception
     */
    public static function get_literature_for_approve($user_id, $rpd_id, $guid): array
    {
        $params = self::validate_parameters(
            self::get_literature_for_approve_parameters(),
            [
                'user_id' => $user_id,
                'rpd_id' => $rpd_id,
                'guid' => $guid
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_literature_for_approve')->request($options);
        $list_literature = $request->get_request_result()->to_array();
        /* $services = new integration_1c_general_ver_1("/GetLiteratureForApproval", "manageRPD");
         $list = ($services->send_to_1c($params));*/

        return $list_literature;
    }

    public static function get_literature_for_approve_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'status' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, '3'),
                'result' => new external_value(PARAM_BOOL, 'result', VALUE_DEFAULT, false),
                'commentary' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, ''),
                'literature' => new external_single_structure(
                    [
                        'mainSelected' => external::get_external_return_literature(),
                        'additionalSelected' => external::get_external_return_literature(),
                        'methodicalSelected' => external::get_external_return_literature(),
                    ],
                    '',
                    VALUE_DEFAULT,
                    []
                ),
            ]
        );
    }


    public static function search_in_1c_library_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'author' => new external_value(PARAM_TEXT, 'author', VALUE_DEFAULT, ''),
                'name' => new external_value(PARAM_TEXT, 'name', VALUE_DEFAULT, ''),
            ]
        );
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws cdo_config_exception
     * @throws invalid_parameter_exception
     */
    public static function search_in_1c_library($author, $name): array
    {
        $params = self::validate_parameters(
            self::search_in_1c_library_parameters(),
            [
                'author' => $author,
                'name' => $name
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('search_in_1c_library')->request($options);
        $data = $request->get_request_result()->to_array();
        $result = [];
        foreach ($data['data'] as $item) {
            $result[] = [
                'approval' => $item['approval'],
                'author' => $item['author'],
                'book' => $item['name'], // . ' ' . $item['publishing'] . ' ' . $item['year'] . ' ' . $item['link'] . ' Количество: ' . $item['count'] . ' '. $item['place_publication'],
                'commentary' => $item['commentary'],
                'count' => $item['count'],
                'id' => $item['ISBN'],
                'link' => $item['link'],
                'publishing' => $item['publishing'],
                'year' => $item['year'],
                'name' => $item['name'],
            ];
        }
        return $result;
    }

    public static function search_in_1c_library_returns()
    {

    }

    public static function get_list_library_workers_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            []
        );
    }

    /**
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function get_list_library_workers(): array
    {
        $params = self::validate_parameters(self::get_list_library_workers_parameters(),
            []
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_list_library_workers')->request($options);
        $data = $request->get_request_result()->to_array();
        return $data;
    }

    public static function get_list_library_workers_returns(): void
    {
    }

    public static function get_list_specialities_for_distribution_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            []
        );
    }

    /**
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function get_list_specialities_for_distribution(): array
    {
        $params = self::validate_parameters(self::get_list_library_workers_parameters(),
            []
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_list_specialities_for_distribution')->request($options);
        $data = $request->get_request_result()->to_array();
        return $data;
    }

    public static function get_list_specialities_for_distribution_returns(): void
    {

    }

    public static function add_worker_on_special_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_TEXT, 'id', VALUE_REQUIRED),
                'spec' => new external_value(PARAM_TEXT, 'id', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * @param $id
     * @param $spec
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function add_worker_on_special($id, $spec): array
    {
        global $USER;
        $params = self::validate_parameters(self::add_worker_on_special_parameters(),
            [
                'id' => $id,
                'spec' => $spec
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('add_worker_on_special')->request($options);
        $data = $request->get_request_result()->to_array();
        return $data;

    }

    public static function add_worker_on_special_returns()
    {

    }

    public static function get_list_rpd_for_library_worker_on_specialty_parameters(): external_function_parameters
    {
        global $USER;
        return new external_function_parameters(
            [
                'user_id' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, $USER->id)
            ]
        );
    }

    /**
     * @param $user_id
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function get_list_rpd_for_library_worker_on_specialty($user_id): array
    {
        global $USER;
        $user_id = $USER->id;
        $params = self::validate_parameters(self::get_list_rpd_for_library_worker_on_specialty_parameters(),
            [
                'user_id' => $user_id
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_list_rpd_for_library_worker_on_specialty')->request($options);
        $list = $request->get_request_result()->to_array();
        foreach ($list as $item) {

            $item['typeAndModule'] = $item->type;
            $item['status'] = (string)$item->status;
            $item['modules'] = [];

        }
        return $list;
    }

    public static function get_list_rpd_for_library_worker_on_specialty_returns()
    {
    }

    public static function send_literature_for_approve_parameters(): external_function_parameters
    {
        global $USER;
        return new external_function_parameters(
            [
                'JSON' => new external_value(PARAM_TEXT, 'id', VALUE_OPTIONAL),
                'user_id' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, $USER->id),
            ]
        );
    }

    /**
     * @param $JSON
     * @param $user_id
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function send_literature_for_approve($JSON, $user_id): array
    {
        global $USER;
        $user_id = $USER->id;
        $params = self::validate_parameters(self::send_literature_for_approve_parameters(),
            [
                'JSON' => $JSON,
                'user_id' => $user_id
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $options->set_parameters_in_json();
        $request = di::get_instance()->get_request('send_literature_for_approve')->request($options);
        $list = $request->get_request_result()->to_array();
        return $list;
    }

    public static function send_literature_for_approve_returns()
    {
    }
}
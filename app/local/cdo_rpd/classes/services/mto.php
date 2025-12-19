<?php

namespace local_cdo_rpd\services;

use coding_exception;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

class mto extends external_api
{
    public static function get_building_from_1c_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'mode' => new external_value(PARAM_RAW, 'mode', VALUE_DEFAULT, "table"),
                'structure_type' => new external_value(PARAM_RAW, 'structure_type', VALUE_DEFAULT, "building")
            ]
        );
    }

    /**
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function get_building_from_1c(): array
    {
        $params = self::validate_parameters(self::get_building_from_1c_parameters(),
            []
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_building_from_1c')->request($options);
        $result = $request->get_request_result()->to_array();
        $building = [];
        foreach ($result['data']['building'] as $datum) {
            $building[] = $datum['element'];
        }
        return $building;
    }

    public static function get_building_from_1c_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'code' => new external_value(PARAM_TEXT, 'code', VALUE_REQUIRED),
                    'name' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                    'uid' => new external_value(PARAM_TEXT, 'uid', VALUE_REQUIRED)
                ]
            ),
            ''
        );
    }

    public static function get_auditorium_by_parent_building_from_1c_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'parent_uid' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                'mode' => new external_value(PARAM_RAW, 'mode', VALUE_DEFAULT, "table"),
                'structure_type' => new external_value(PARAM_RAW, 'structure_type', VALUE_DEFAULT, "room")
            ]
        );
    }

    /**
     * @param $parent_uid
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function get_auditorium_by_parent_building_from_1c($parent_uid): array
    {
        $params = self::validate_parameters(self::get_auditorium_by_parent_building_from_1c_parameters(),
            [
                'parent_uid' => $parent_uid,
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties(
            $params
        );
        $request = di::get_instance()
            ->get_request('get_building_from_1c')
            ->request($options);
        $building = [];
        $result = $request->get_request_result()->to_array();
        foreach ($result['data']['room'] as $datum) {
            $building[] = $datum['element'];
        }
        return $building;
    }

    public static function get_auditorium_by_parent_building_from_1c_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'code' => new external_value(PARAM_TEXT, 'code', VALUE_REQUIRED),
                    'name' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                    'uid' => new external_value(PARAM_TEXT, 'uid', VALUE_REQUIRED)
                ]
            ),
            ''
        );
    }

    public static function get_software_by_auditorium_from_1c_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'location_room_uid' => new external_value(PARAM_TEXT, 'structure_type', VALUE_REQUIRED),
                'mode' => new external_value(PARAM_TEXT, 'mode', VALUE_DEFAULT, "software_by_location"),
                'person_id' => new external_value(PARAM_TEXT, 'structure_type', VALUE_DEFAULT, "000014567")
            ]
        );
    }

    /**
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function get_software_by_auditorium_from_1c($location_room_uid): array
    {
        $params = self::validate_parameters(self::get_software_by_auditorium_from_1c_parameters(),
            [
                'location_room_uid' => $location_room_uid,
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties(
            $params
        );
        $request = di::get_instance()
            ->get_request('get_software_by_auditorium_from_1c')
            ->request($options);
        $software = [];
        $result = $request->get_request_result()->to_array();
        foreach ($result['data']['software'] as $datum) {
            $software[] = $datum['description'];
        }
        return $software;
    }

    public static function get_software_by_auditorium_from_1c_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'fullname' => new external_value(PARAM_TEXT, 'fullname', VALUE_REQUIRED),
                    'uid' => new external_value(PARAM_TEXT, 'uid', VALUE_REQUIRED),
                ]
            ),
            ''
        );
    }

    public static function get_inventory_by_auditorium_from_1c_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'location_room_uid' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                'mode' => new external_value(PARAM_TEXT, 'mode', VALUE_DEFAULT, "inventory_by_location"),
                'person_id' => new external_value(PARAM_TEXT, 'structure_type', VALUE_DEFAULT, "000014567")
            ]
        );
    }

    /**
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function get_inventory_by_auditorium_from_1c($location_room_uid): array
    {
        $params = self::validate_parameters(self::get_inventory_by_auditorium_from_1c_parameters(),
            [
                'location_room_uid' => $location_room_uid,
            ]
        );
        $options = di::get_instance()->get_request_options();
        $options->set_properties(
            $params
        );
        $request = di::get_instance()
            ->get_request('get_inventory_by_auditorium_from_1c')
            ->request($options);
        $inventory = [];
        $result = $request->get_request_result()->to_array();
        foreach ($result['data']['inventory'] as $datum) {
            $inventory[] = $datum['element'];
        }
        return $inventory;
    }

    public static function get_inventory_by_auditorium_from_1c_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'fullname' => new external_value(PARAM_TEXT, 'code', VALUE_REQUIRED),
                    'uid' => new external_value(PARAM_TEXT, 'uid', VALUE_REQUIRED)
                ]
            ),
            '',
            VALUE_DEFAULT,
            []
        );
    }
}
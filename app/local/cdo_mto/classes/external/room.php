<?php

namespace local_cdo_mto\external;

use coding_exception;
use local_cdo_mto\services\controller;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

use invalid_parameter_exception;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\response_dto;

class room extends external_api
{
  private const API_METHODS = [
    'GET_ROOM_INFO' => 'get_MTO_room_info',
    'CREATE_ROOM'   => 'post_MTO_create_room',
    'PATCH_ROOM'    => 'patch_MTO_room',
  ];

  /**
   * @return external_single_structure
   */
  private static function error_structure(): external_single_structure
  {
    return new external_single_structure([
      'error' => new external_value(PARAM_TEXT, 'Error message', VALUE_DEFAULT, '')
    ]);
  }

  /**
   * @return external_function_parameters
   */
  public static function get_room_info_parameters(): external_function_parameters
  {
    return new external_function_parameters([
      'mode' => new external_value(PARAM_TEXT, 'mode', VALUE_REQUIRED),
      'structure_type' => new external_value(PARAM_TEXT, 'mode', VALUE_REQUIRED),
    ]);
  }

  /**
   * @return external_function_parameters
   */
  public static function patch_room_parameters(): external_function_parameters
  {
    return new external_function_parameters([
      'object_name'  => new external_value(PARAM_TEXT, 'Object name', VALUE_REQUIRED),
      'building_uid' => new external_value(PARAM_TEXT, 'Building UID', VALUE_OPTIONAL),
      'object_uid'   => new external_value(PARAM_TEXT, 'Object UID', VALUE_OPTIONAL),
      'user_id'      => new external_value(PARAM_TEXT, 'User ID', VALUE_OPTIONAL),
      'room_capacity'    => new external_value(PARAM_TEXT, 'Room capacity', VALUE_OPTIONAL),
      'room_area'        => new external_value(PARAM_TEXT, 'Room area', VALUE_OPTIONAL),
      'room_number'      => new external_value(PARAM_TEXT, 'Room number', VALUE_OPTIONAL),
      'room_technumber'  => new external_value(PARAM_TEXT, 'Room technical number', VALUE_OPTIONAL),
      'room_description' => new external_value(PARAM_TEXT, 'Room description', VALUE_OPTIONAL),
    ]);
  }

  /**
   * @return external_function_parameters
   */
  public static function add_room_parameters(): external_function_parameters
  {
    return new external_function_parameters([
      'object_name'  => new external_value(PARAM_TEXT, 'Object name', VALUE_REQUIRED),
      'building_uid' => new external_value(PARAM_TEXT, 'Building UID', VALUE_REQUIRED),
      'user_id'      => new external_value(PARAM_TEXT, 'User ID', VALUE_OPTIONAL),
      'room_capacity'    => new external_value(PARAM_TEXT, 'Room capacity', VALUE_OPTIONAL),
      'room_area'        => new external_value(PARAM_TEXT, 'Room area', VALUE_OPTIONAL),
      'room_number'      => new external_value(PARAM_TEXT, 'Room number', VALUE_OPTIONAL),
      'room_technumber'  => new external_value(PARAM_TEXT, 'Room technical number', VALUE_OPTIONAL),
      'room_description' => new external_value(PARAM_TEXT, 'Room description', VALUE_OPTIONAL),
    ]);
  }

  /**
   * @throws cdo_type_response_exception
   * @throws coding_exception
   * @throws cdo_config_exception
   * @throws invalid_parameter_exception
   */
  public static function get_room_info(string $mode, string $structure_type): array
  {
      $params = self::validate_parameters(self::get_room_info_parameters(), [
        'mode'           => $mode,
        'structure_type' => $structure_type,
      ]);

      $controller = new controller();
      return $controller->get_structure_info_api($params, self::API_METHODS['GET_ROOM_INFO'])->to_array();
  }

  /**
   * @param string $object_name
   * @param string $building_uid
   * @param string|null $room_capacity
   * @param string|null $room_area
   * @param string|null $room_number
   * @param string|null $building_owner
   * @param string|null $room_description
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   * @throws invalid_parameter_exception
   */
  public static function add_room(
    string $object_name,
    string $building_uid,
    string $room_capacity = null,
    string $room_area = null,
    string $room_number = null,
    string $building_owner = null,
    string $room_description = null
  ): response_dto
  {
      global $USER;
      //    $user_id = $USER->id;
    $user_id = 6;

      $params = self::validate_parameters(self::add_room_parameters(), [
        'object_name'      => $object_name,
        'building_uid'     => $building_uid,
        'user_id'          => $user_id,
        'room_capacity'    => $room_capacity,
        'room_area'        => $room_area,
        'room_number'      => $room_number,
        'room_technumber'  => $building_owner,
        'room_description' => $room_description,
      ]);

      $controller = new controller();
      return $controller->create_room_api($params, self::API_METHODS['CREATE_ROOM']);
  }

  /**
   * @param string $object_name
   * @param string $building_uid
   * @param string $object_uid
   * @param string|null $room_capacity
   * @param string|null $room_area
   * @param string|null $room_number
   * @param string|null $building_owner
   * @param string|null $room_description
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   * @throws invalid_parameter_exception
   */
  public static function patch_room(
    string $object_name,
    string $building_uid,
    string $object_uid,
    string $room_capacity = null,
    string $room_area = null,
    string $room_number = null,
    string $building_owner = null,
    string $room_description = null
  ): response_dto
  {
      global $USER;
      //    $user_id = $USER->id;
    $user_id = 6;

      $params = self::validate_parameters(self::patch_room_parameters(), [
        'object_name'      => $object_name,
        'building_uid'     => $building_uid,
        'object_uid'       => $object_uid,
        'user_id'          => $user_id,
        'room_capacity'    => $room_capacity,
        'room_area'        => $room_area,
        'room_number'      => $room_number,
        'room_technumber'  => $building_owner,
        'room_description' => $room_description,
      ]);

      $controller = new controller();
      return $controller->patch_room_api($params, self::API_METHODS['PATCH_ROOM']);
  }

  /**
   * @return external_single_structure
   */
  public static function get_room_info_returns(): external_single_structure
  {
    return new external_single_structure([
      'data' => new external_single_structure(
        [
          'room' => new external_multiple_structure(
            new external_single_structure(
              [
                'element_type' => new external_value(PARAM_TEXT, 'element_type', VALUE_REQUIRED),
                'element' => new external_single_structure(
                  [
                    'uid'   => new external_value(PARAM_TEXT, 'uid', VALUE_REQUIRED),
                    'code'  => new external_value(PARAM_TEXT, 'code', VALUE_REQUIRED),
                    'name'  => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                  ]
                ),
                'parent' => new external_single_structure(
                  [
                    'uid'   => new external_value(PARAM_TEXT, 'uid', VALUE_REQUIRED),
                    'code'  => new external_value(PARAM_TEXT, 'code', VALUE_REQUIRED),
                    'name'  => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                    'type'  => new external_value(PARAM_TEXT, 'type', VALUE_DEFAULT, ''),
                  ]
                ),
                'element_characteristics' => new external_single_structure(
                  [
                    'room_capacity' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'room_area' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'room_special' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'room_number' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'room_technumber' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'room_description' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'room_equipment' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'room_type' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                  ]
                ),
              ]
            ),
            '',
            VALUE_DEFAULT,
            []
          )
        ]
      ),
      'error' => new external_value(PARAM_TEXT, 'error', VALUE_REQUIRED)
    ]);
  }

  /**
   * @return external_single_structure
   */
  public static function add_room_returns(): external_single_structure
  {
    return self::error_structure();
  }

  /**
   * @return external_single_structure
   */
  public static function patch_room_returns(): external_single_structure
  {
    return self::error_structure();
  }
}

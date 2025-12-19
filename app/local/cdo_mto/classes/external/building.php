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

class building extends external_api
{
  private const API_METHODS = [
    'GET_BUILDING_INFO' => 'get_MTO_building_info',
    'CREATE_BUILDING'   => 'post_MTO_create_building',
    'PATCH_BUILDING'    => 'patch_MTO_building',
    'DELETE_BUILDING'   => 'del_MTO_building',
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
  public static function get_building_info_parameters(): external_function_parameters
  {
    return new external_function_parameters(
      [
        'mode' => new external_value(PARAM_TEXT, 'mode', VALUE_REQUIRED),
        'structure_type' => new external_value(PARAM_TEXT, 'mode', VALUE_REQUIRED),
      ]
    );
  }

  /**
   * @return external_function_parameters
   */
  public static function patch_building_parameters(): external_function_parameters
  {
    return new external_function_parameters(
      [
        'object_name' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
        'object_uid'  => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'user_id'     => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),

        'building_address'   => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_docsanit'  => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_docfire'   => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_owner'     => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_usagedoc'  => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_cadastre'  => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_usagetype' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_registry'  => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_purpose'   => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_4disabled' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
      ]
    );
  }

  /**
   * @return external_function_parameters
   */
  public static function add_building_parameters(): external_function_parameters
  {
    return new external_function_parameters(
      [
        'object_name' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
        'user_id'     => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),

        'building_address'   => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_docsanit'  => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_docfire'   => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_owner'     => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_usagedoc'  => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_cadastre'  => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_usagetype' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_registry'  => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_purpose'   => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'building_4disabled' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
      ]
    );
  }

  /**
   * @return external_function_parameters
   */
  public static function del_building_parameters(): external_function_parameters
  {
    return new external_function_parameters(
      [
        'object_uid' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
        'user_id'    => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'mode'       => new external_value(PARAM_TEXT, '', VALUE_DEFAULT, 'hierarchy')
      ]
    );
  }


  /**
   * @param $mode
   * @param $structure_type
   * @return array
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   * @throws invalid_parameter_exception
   */
  public static function get_building_info($mode, $structure_type): array
  {
    $params = self::validate_parameters(self::get_building_info_parameters(),
      [
        'mode' => $mode,
        'structure_type' => $structure_type,
      ]
    );
    $controller = new controller();
    return $controller->get_structure_info_api($params, self::API_METHODS['GET_BUILDING_INFO'])->to_array();
  }


  /**
   * @param string $object_name
   * @param string|null $building_address
   * @param string|null $building_docsanit
   * @param string|null $building_docfire
   * @param string|null $building_owner
   * @param string|null $building_usagedoc
   * @param string|null $building_cadastre
   * @param string|null $building_usagetype
   * @param string|null $building_registry
   * @param string|null $building_purpose
   * @param string|null $building_4disabled
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   * @throws invalid_parameter_exception
   */
  public static function add_building(
    string $object_name,
    string $building_address = null,
    string $building_docsanit = null,
    string $building_docfire = null,
    string $building_owner = null,
    string $building_usagedoc = null,
    string $building_cadastre = null,
    string $building_usagetype = null,
    string $building_registry = null,
    string $building_purpose = null,
    string $building_4disabled = null
  ): response_dto
  {
    global $USER;
//    $user_id = $USER->id;
    $user_id = 6;

    $params = self::validate_parameters(self::add_building_parameters(),
      [
        'object_name' => $object_name,
        'user_id'     => $user_id,

        'building_address'   => $building_address,
        'building_docsanit'  => $building_docsanit,
        'building_docfire'   => $building_docfire,
        'building_owner'     => $building_owner,
        'building_usagedoc'  => $building_usagedoc,
        'building_cadastre'  => $building_cadastre,
        'building_usagetype' => $building_usagetype,
        'building_registry'  => $building_registry,
        'building_purpose'   => $building_purpose,
        'building_4disabled' => $building_4disabled
      ]
    );

    $controller = new controller();
    return $controller->create_building_api($params, self::API_METHODS['CREATE_BUILDING']) ;
  }


  /**
   * @param string $object_name
   * @param string $object_uid
   * @param string|null $building_address
   * @param string|null $building_docsanit
   * @param string|null $building_docfire
   * @param string|null $building_owner
   * @param string|null $building_usagedoc
   * @param string|null $building_cadastre
   * @param string|null $building_usagetype
   * @param string|null $building_registry
   * @param string|null $building_purpose
   * @param string|null $building_4disabled
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   * @throws invalid_parameter_exception
   */
  public static function patch_building(
    string $object_name,
    string $object_uid,
    string $building_address = null,
    string $building_docsanit = null,
    string $building_docfire = null,
    string $building_owner = null,
    string $building_usagedoc = null,
    string $building_cadastre = null,
    string $building_usagetype = null,
    string $building_registry = null,
    string $building_purpose = null,
    string $building_4disabled = null
  ): response_dto
  {
    global $USER;
//    $user_id = $USER->id;
    $user_id = 6;

    $params = self::validate_parameters(self::patch_building_parameters(),
      [
        'object_name'    => $object_name,
        'object_uid'     => $object_uid,
        'user_id'        => $user_id,

        'building_address'   => $building_address,
        'building_docsanit'  => $building_docsanit,
        'building_docfire'   => $building_docfire,
        'building_owner'     => $building_owner,
        'building_usagedoc'  => $building_usagedoc,
        'building_cadastre'  => $building_cadastre,
        'building_usagetype' => $building_usagetype,
        'building_registry'  => $building_registry,
        'building_purpose'   => $building_purpose,
        'building_4disabled' => $building_4disabled
      ]
    );

    $controller = new controller();
    return $controller->patch_building_api($params, self::API_METHODS['PATCH_BUILDING']);
  }

  /**
   * @param $object_uid
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   * @throws invalid_parameter_exception
   */
  public static function del_building( $object_uid  ): response_dto {
    global $USER;
//    $user_id = $USER->id;
    $user_id = 6;

    $params = self::validate_parameters(self::del_building_parameters(),
      [
        'object_uid' => $object_uid,
        'user_id'    => $user_id,
        'mode'       => 'hierarchy'
      ]
    );

    $controller = new controller();
    return $controller->get_structure_info_api($params, self::API_METHODS['DELETE_BUILDING']);
  }

  /**
   * @return external_single_structure
   */
  public static function get_building_info_returns(): external_single_structure
  {
    return new external_single_structure([
      'data' => new external_single_structure(
        [
          'building' => new external_multiple_structure(
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
                'element_characteristics' => new external_single_structure(
                  [
                    'building_address' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'building_docsanit' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'building_docfire' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'building_owner' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'building_cadastre' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'building_usagedoc' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'building_usagetype' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'building_registry' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'building_purpose' => new external_single_structure(
                      [
                        'value' => new external_value(PARAM_RAW, 'value', VALUE_DEFAULT, ''),
                        'quantity' => new external_value(PARAM_TEXT, 'quantity', VALUE_DEFAULT, ''),
                      ]
                    ),
                    'building_4disabled' => new external_single_structure(
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
  public static function patch_building_returns(): external_single_structure {
    return self::error_structure();
  }

  /**
   * @return external_single_structure
   */
  public static function add_building_returns() : external_single_structure {
    return self::error_structure();
  }

  /**
   * @return external_single_structure
   */
  public static function del_building_returns(): external_single_structure {
    return self::error_structure();
  }

}

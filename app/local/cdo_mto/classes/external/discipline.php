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

class discipline extends external_api
{
  private const API_METHODS = [
    'GET'   => 'get_MTO_discipline_info',
    'PATCH' => 'patch_MTO_discipline',
  ];

  /**
   * @return external_function_parameters
   */
  public static function get_discipline_info_parameters(): external_function_parameters
  {
    return new external_function_parameters(
      [
        'mode' => new external_value(PARAM_TEXT, 'mode', VALUE_REQUIRED),
        'structure_type' => new external_value(PARAM_TEXT, 'structure_type', VALUE_REQUIRED),
      ]
    );
  }

  /**
   * @return external_function_parameters
   */
  public static function patch_discipline_parameters(): external_function_parameters
  {
    return new external_function_parameters(
      [
        'uid'     => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
        'name'    => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
        'user_id' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
      ]
    );
  }

  /**
   * @param string $mode
   * @param string $structure_type
   * @return array
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   * @throws invalid_parameter_exception
   */
  public static function get_discipline_info(string $mode, string $structure_type): array
  {
    $params = self::validate_parameters(self::get_discipline_info_parameters(),
      [
        'mode' => $mode,
        'structure_type' => $structure_type
      ]
    );
    $controller = new controller();
    return $controller->get_structure_info_api($params, self::API_METHODS['GET'])->to_array();
  }

  /**
   * @param string $uid
   * @param string $name
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   * @throws invalid_parameter_exception
   */
  public static function patch_discipline (
    string $uid,
    string $name
  ): response_dto
  {
    global $USER;
//    $user_id = $USER->id;
    $user_id = 6;

    $params = self::validate_parameters(self::patch_discipline_parameters(),
      [
        'user_id' => $user_id,
        'uid'     => $uid,
        'name'    => $name,
      ]
    );

    $controller = new controller();
    return $controller->patch_discipline_api($params, self::API_METHODS['PATCH']);
  }

  /**
   * @return external_single_structure
   */
  public static function patch_discipline_returns(): external_single_structure {

    return new external_single_structure([
      'error' => new external_value(PARAM_TEXT, 'error',  VALUE_DEFAULT, '')
    ]);
  }

  /**
   * @return external_single_structure
   */
  public static function get_discipline_info_returns(): external_single_structure
  {
    return new external_single_structure([
      'data' => new external_single_structure(
        [
          'education_program' => new external_multiple_structure(
            new external_single_structure(
              [
                'uid' => new external_value(PARAM_TEXT, 'uid', VALUE_DEFAULT, ''),
                'name' => new external_value(PARAM_TEXT, 'name', VALUE_DEFAULT, ''),
                'academic_year' => new external_single_structure(
                  [
                    'uid'   => new external_value(PARAM_TEXT, 'uid', VALUE_DEFAULT, ''),
                    'name'  => new external_value(PARAM_TEXT, 'name', VALUE_DEFAULT, ''),
                  ]
                ),
                'speciality' => new external_single_structure(
                  [
                    'uid'   => new external_value(PARAM_TEXT, 'uid', VALUE_DEFAULT, ''),
                    'name'  => new external_value(PARAM_TEXT, 'name', VALUE_DEFAULT, ''),
                  ]
                ),
                'specialisation' => new external_single_structure(
                  [
                    'uid'   => new external_value(PARAM_TEXT, 'uid', VALUE_DEFAULT, ''),
                    'name'  => new external_value(PARAM_TEXT, 'name', VALUE_DEFAULT, ''),
                  ]
                ),
                'discipline' => new external_multiple_structure(
                  new external_single_structure( [
                    'uid'   => new external_value(PARAM_TEXT, 'uid', VALUE_DEFAULT, ''),
                    'name'  => new external_value(PARAM_TEXT, 'name', VALUE_DEFAULT, ''),
                  ] ),
                  'Массив Дисциплин'
                )
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


}

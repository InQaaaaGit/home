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

class eduProgram extends external_api
{
  private const API_METHODS = [
    'GET'   => 'get_MTO_edu_program_info',
    'PATCH' => 'patch_MTO_edu_program',
  ];

  /**
   * @return external_function_parameters
   */
  public static function get_edu_program_info_parameters(): external_function_parameters
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
  public static function patch_education_program_parameters(): external_function_parameters
  {
    return new external_function_parameters(
      [
        'specialty_guid' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
        'newnamespecialty' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
        'profile_guid' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
        'newnameprofile' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
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
  public static function get_edu_program_info(string $mode, string $structure_type): array
  {
    $params = self::validate_parameters(self::get_edu_program_info_parameters(),
      [
        'mode' => $mode,
        'structure_type' => $structure_type,
      ]
    );
    $controller = new controller();
    return $controller->get_structure_info_api($params, self::API_METHODS['GET'])->to_array();
  }

  /**
   * @param string $specialty_guid
   * @param string $newnamespecialty
   * @param string|null $profile_guid
   * @param string|null $newnameprofile
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   * @throws invalid_parameter_exception
   */
  public static function patch_education_program (
    string $specialty_guid,
    string $newnamespecialty,
    string $profile_guid = null,
    string $newnameprofile = null
  ): response_dto
  {
    $params = self::validate_parameters(self::patch_education_program_parameters(),
      [
        'specialty_guid' => $specialty_guid,
        'newnamespecialty' => $newnamespecialty,
        'profile_guid' => $profile_guid,
        'newnameprofile' => $newnameprofile,
      ]
    );

    $controller = new controller();
    return $controller->get_structure_info_api($params, self::API_METHODS['PATCH']);
  }

  /**
   * @return external_multiple_structure
   */
  public static function get_edu_program_info_returns(): external_multiple_structure {
    return new external_multiple_structure(
      new external_single_structure( [
        'EducationalProgram' => new external_value(PARAM_TEXT, 'Название образовательной программы'),
        'Specialty' => new external_value(PARAM_TEXT, 'Специальность'),
        'Profile' => new external_value(PARAM_TEXT, 'Профиль', VALUE_DEFAULT, ''),
        'YearSet' => new external_value(PARAM_TEXT, 'Год набора'),
        'Realize' => new external_value(PARAM_TEXT, 'Реализация', VALUE_DEFAULT, ''),
        'Guid_Specialty' => new external_value(PARAM_TEXT, 'GUID специальности'),
        'Guid_Profile' => new external_value(PARAM_TEXT, 'GUID профиля', VALUE_DEFAULT, '')
      ] ),
      'Массив образовательных программ'
    );
  }

  /**
   * @return external_single_structure
   */
  public static function patch_education_program_returns(): external_single_structure {

    return new external_single_structure([
      'error' => new external_value(PARAM_TEXT, 'error',  VALUE_DEFAULT, '')
    ]);
  }

}

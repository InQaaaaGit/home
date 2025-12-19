<?php

namespace local_cdo_mto\external;

use coding_exception;
use local_cdo_mto\services\controller;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\response_dto;

class main extends external_api
{
    public static function get_structures_info_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'mode' => new external_value(PARAM_TEXT, 'mode', VALUE_REQUIRED),
                'structure_type' => new external_value(PARAM_TEXT, 'mode', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @param $mode
     * @param $structure_type
     * @return response_dto
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws invalid_parameter_exception
     * @throws coding_exception
     */
    public static function get_structures_info($mode, $structure_type): array
    {
        $params = self::validate_parameters(self::get_structures_info_parameters(),
            [
                'mode' => $mode,
                'structure_type' => $structure_type,
            ]
        );

        $controller = new controller();
        return $controller->get_structure_info_api($params)->to_array();
    }

    public static function get_structures_info_returns(): external_single_structure
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
                                        'building_address'      => new external_value(PARAM_TEXT, 'building_address', VALUE_DEFAULT, ''),
                                        'building_docsanit'     => new external_value(PARAM_TEXT, 'building_address', VALUE_DEFAULT, ''),
                                        'building_docfire'      => new external_value(PARAM_TEXT, 'building_address', VALUE_DEFAULT, ''),
                                        'building_owner'        => new external_value(PARAM_TEXT, 'building_address', VALUE_DEFAULT, ''),
                                        'building_cadastre'     => new external_value(PARAM_TEXT, 'building_address', VALUE_DEFAULT, ''),
                                        'building_usagedoc'     => new external_value(PARAM_TEXT, 'building_address', VALUE_DEFAULT, ''),
                                        'building_usagetype'    => new external_value(PARAM_TEXT, 'building_address', VALUE_DEFAULT, ''),
                                        'building_registry'     => new external_value(PARAM_TEXT, 'building_address', VALUE_DEFAULT, ''),
                                        'building_purpose'      => new external_value(PARAM_TEXT, 'building_address', VALUE_DEFAULT, ''),
                                        'building_4disabled'    => new external_value(PARAM_TEXT, 'building_address', VALUE_DEFAULT, ''),
                                    ]
                                )
                            ]
                        ),
                        '',
                        VALUE_DEFAULT,
                        []
                    ),
                    'room' => new external_multiple_structure(
                        new external_single_structure(
                            []
                        ),
                        '',
                        VALUE_DEFAULT,
                        []
                    ),
                ]
            ),
            'error' => new external_value(PARAM_BOOL, 'status', VALUE_REQUIRED)
        ]);
    }
}
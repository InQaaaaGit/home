<?php

namespace tool_cdo_config\external;

use auth_plugin_manual;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;
use tool_cdo_config\tools\dumper;
use function src\transformer\utils\get_user;

class authorization extends external_api
{
    public static function authorized_users_parameters()
    {
        return new external_function_parameters(
            [
                'login' => new external_value(PARAM_TEXT, 'login', VALUE_REQUIRED),
                'password' => new external_value(PARAM_TEXT, 'password', VALUE_REQUIRED)
            ]
        );
    }

    public static function authorized_users($login, $password)
    {
        $params = self::validate_parameters(self::authorized_users_parameters(),
            [
                'login' => $login,
                'password' => $password
            ]
        );

        $exist = (new auth_plugin_manual())->user_login($params['login'], $params['password']);
        $user_return = [];
        if ($exist) {
            $user = get_complete_user_data('username', $params['login']);
            $user_return['id'] = $user->id;
            $user_return['code'] = $user->lastnamephonetic;
            $user_return['type'] = json_decode($user->profile['typelk']) ?? [];
            $user_return['gradebooks'] = json_decode($user->profile['gradebook']) ?? [];
        }

        return $user_return;

    }

    public static function authorized_users_returns()
    {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'id', VALUE_OPTIONAL),
                'code' => new external_value(PARAM_TEXT, 'code', VALUE_OPTIONAL),
                'type' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'choosen' => new external_value(PARAM_BOOL, 'code', VALUE_OPTIONAL),
                            'type' => new external_value(PARAM_TEXT, 'type', VALUE_OPTIONAL),
                            'name' => new external_value(PARAM_TEXT, 'name', VALUE_OPTIONAL),
                        ]
                    ),
                    'aslkfjasdf',
                    false,
                    []
                ),
                'gradebooks' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'priority' => new external_value(PARAM_INT, 'code', VALUE_OPTIONAL),
                            'name' => new external_value(PARAM_TEXT, 'name', VALUE_OPTIONAL),
                            'code' => new external_value(PARAM_TEXT, 'code', VALUE_OPTIONAL),
                            'groupcode' => new external_value(PARAM_TEXT, 'groupcode', VALUE_OPTIONAL),
                            'groupname' => new external_value(PARAM_TEXT, 'groupname', VALUE_OPTIONAL),
                        ]
                    ),
                    'sdfasdf',
                    false,
                    []
                )
            ],
            'sadlkfjalskdjf',
            false,
            []
        );

    }

}
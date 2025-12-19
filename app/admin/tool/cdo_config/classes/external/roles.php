<?php

namespace tool_cdo_config\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

class roles extends external_api {

	public static function get_eios_roles_parameters(): external_function_parameters {
		return new external_function_parameters([], 'Аргументы запроса отсутствуют', VALUE_OPTIONAL);
	}

	public static function get_eios_roles() {
        global $DB;

        return $DB->get_records('role', null, '', 'id, name, shortname');
	}

	public static function get_eios_roles_returns(): external_multiple_structure {
		return new external_multiple_structure(
			new external_single_structure(
				[
					"id" => new external_value(PARAM_INT, 'Идентификатор роли'),
					"name" => new external_value(PARAM_TEXT, 'Название роли'),
					"shortname" => new external_value(PARAM_TEXT, 'Краткое название роли'),
				],
				'Информация по роле'
			),
		'Список созданных ролей'
		);
	}

}

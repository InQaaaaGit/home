<?php

namespace block_cdo_seamless_transition\external;

use block_cdo_seamless_transition\transitions\factory_transitions;
use coding_exception;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use tool_cdo_config\exceptions\cdo_config_exception;

class transition_api extends external_api {

	/**
	 * @return external_function_parameters
	 * @throws coding_exception
	 */
	public static function get_transition_parameters(): external_function_parameters {
		return new external_function_parameters(
			[
				'transition_code' => new external_value(
					PARAM_TEXT, get_string('transition_api_code', 'block_cdo_seamless_transition')
				),
				'transition_params' => new external_value(
					PARAM_TEXT, get_string('transition_api_params_json', 'block_cdo_seamless_transition')
				)
			],
			get_string('transition_api_link_to', 'block_cdo_seamless_transition')
		);
	}

	/**
	 * @param string $transition_code
	 * @param string $transition_params
	 * @return array
	 * @throws cdo_config_exception
	 */
	public static function get_transition(string $transition_code, string $transition_params): array {

		$transition = factory_transitions::get_instance()->get_transition($transition_code);

		if (is_null($transition)) {
			throw new cdo_config_exception(3005);
		}

		return ['transition_to' => $transition->to()];
	}

	/**
	 * @return external_single_structure
	 * @throws coding_exception
	 */
	public static function get_transition_returns(): external_single_structure {
		return new external_single_structure(
			[
				'transition_to' => new external_value(
					PARAM_TEXT, get_string('transition_api_link_to_redirect', 'block_cdo_seamless_transition')
				)
			],
			get_string('transition_api_answer', 'block_cdo_seamless_transition')
		);
	}
}

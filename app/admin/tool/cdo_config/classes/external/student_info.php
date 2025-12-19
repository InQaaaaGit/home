<?php

namespace tool_cdoconfig\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;

class student_info extends external_api{

	public static function get_parameters() {

		$parameters_id = ['id' => new external_value(PARAM_TEXT, 'Код пользователя')];

		$parameters = new external_single_structure(
			$parameters_id,
			'Параметры для подстановки в запрос',
			VALUE_REQUIRED
		);

		$headers = new external_multiple_structure(
			new external_value(PARAM_TEXT, VALUE_OPTIONAL),
			'',
			VALUE_OPTIONAL
		);

		$options = new external_single_structure(
			['header_replace' => new external_value(PARAM_BOOL, 'Сбросить остальные заголовки')],
			'Дополнительные опции для запроса',
			VALUE_OPTIONAL
		);

		return new external_function_parameters([
			'parameters' => $parameters,
			'headers' => $headers,
			'options' => $options
		]);
	}

	/**
	 * @param $parameters
	 * @param array $headers
	 * @param $options
	 * @return array
	 * @throws cdo_config_exception
	 */
	public static function get($parameters, array $headers = [], $options = null): array {

		$options = (array)$options;

		$request_options = di::get_instance()->get_request_options();
		$request_options->set_properties((array) $parameters);

		if (count($headers)){
			$request_options->set_headers($headers);
		}

		if (array_key_exists('header_replace', $options)) {
			$request_options->set_replace_headers($options['header_replace']);
		}

		if (array_key_exists('debug', $options)) {
			$request_options->set_debug($options['debug']);
		}

		return di::get_instance()->get_request('')->request($request_options)->get_request_result()->to_array();
	}

	public static function get_returns() {
		return null;
	}
}
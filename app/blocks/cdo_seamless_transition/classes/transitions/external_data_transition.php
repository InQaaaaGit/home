<?php

namespace block_cdo_seamless_transition\transitions;

use JsonException;
use stdClass;

class external_data_transition {

	private string $ajax_method;
	private string $transition_code;
	private stdClass $transition_params;
	private string $transition_params_json;

	/**
	 * @return string
	 */
	public function get_ajax_method(): string {
		return $this->ajax_method;
	}

	/**
	 * @return string
	 */
	public function get_transition_code(): string {
		return $this->transition_code;
	}

	/**
	 * @return stdClass
	 */
	public function get_transition_params(): stdClass {
		return $this->transition_params;
	}

	/**
	 * @return string
	 */
	public function get_transition_params_json(): string {
		return $this->transition_params_json;
	}

	/**
	 * @param string $method
	 * @param string $code
	 * @param stdClass $params
	 * @throws JsonException
	 */
	public function __construct(string $method, string $code, stdClass $params) {
		$this->ajax_method = $method;
		$this->transition_code = $code;
		$this->transition_params = $params;
		$this->transition_params_json = json_encode($params, JSON_THROW_ON_ERROR);
	}

	/**
	 * @return stdClass
	 */
	public function to_object(): stdClass {
		$content = new stdClass();
		$content->ajax_method = $this->get_ajax_method();
		$content->transition_code = $this->get_transition_code();
		$content->transition_params = $this->get_transition_params();
		$content->transition_params_json = $this->get_transition_params_json();
		return $content;
	}
}
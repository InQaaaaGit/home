<?php

namespace block_cdo_seamless_transition\transitions;

use block_cdo_seamless_transition\services\consultant_student_client;
use coding_exception;
use JsonException;
use moodle_exception;
use stdClass;
use tool_cdo_config\exceptions\cdo_config_exception;

class consultant_student_transition extends transition {

	/**
	 * @return string
	 * @throws JsonException
	 * @throws coding_exception
	 * @throws moodle_exception
	 * @throws cdo_config_exception
	 */
	public function to(): string {
		$consultant_student_client = new consultant_student_client(
			$this->get_data()->organization,
			$this->get_data()->contract
		);
		return $consultant_student_client->get_response();
	}

	/**
	 * @return string
	 */
	public function get_code(): string {
		return "consultant_student";
	}

	/**
	 * @return string
	 * @throws coding_exception
	 */
	public function get_transition_name(): string {
		return get_string('consultant_student_name', 'block_cdo_seamless_transition');
	}

	/**
	 * @return stdClass
	 */
	public function get_other_external_param(): stdClass {
		return new stdClass();
	}
}
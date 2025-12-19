<?php

namespace block_cdo_seamless_transition\transitions;

use block_cdo_seamless_transition\services\lan_client;
use coding_exception;
use JsonException;
use moodle_exception;
use stdClass;

class lan_transition extends transition {

	/**
	 * @return string
	 * @throws JsonException
	 * @throws moodle_exception
	 */
	public function to(): string {
		return (new lan_client($this->get_data()->token))->get_response();
	}

	/**
	 * @return string
	 */
	public function get_code(): string {
		return "lan";
	}

	/**
	 * @return string
	 * @throws coding_exception
	 */
	public function get_transition_name(): string {
		return get_string('lan_name', 'block_cdo_seamless_transition');
	}

	/**
	 * @return stdClass
	 */
	public function get_other_external_param(): stdClass {
		return new stdClass();
	}
}
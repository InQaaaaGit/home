<?php

namespace block_cdo_seamless_transition\transitions;

use coding_exception;
use moodle_exception;
use moodle_url;
use stdClass;

class yurayt_transition extends transition {

	/**
	 * @return string
	 * @throws moodle_exception
	 */
	public function to(): string {
		global $USER;

		$time = time();
		$url_params = [
			'pid' => $this->get_data()->pid,
			'email' => $USER->email,
			'fname' => $USER->firstname,
			'lname' => $USER->lastname,
			'time' => $time,
			'sign' => md5("{$this->get_data()->pid}:{$USER->email}:{$this->get_data()->token}:{$time}"),
		];
		return (new moodle_url("https://urait.ru/login/partner", $url_params))->out(false);
	}

	/**
	 * @return string
	 */
	public function get_code(): string {
		return "yurayt";
	}

	/**
	 * @return string
	 * @throws coding_exception
	 */
	public function get_transition_name(): string {
		return get_string('yurayt_name', 'block_cdo_seamless_transition');
	}

	/**
	 * @return stdClass
	 */
	public function get_other_external_param(): stdClass {
		return new stdClass();
	}
}
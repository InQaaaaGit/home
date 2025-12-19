<?php

namespace block_cdo_seamless_transition\transitions;

use coding_exception;
use moodle_exception;
use moodle_url;
use stdClass;

class ipr_books_transition extends transition {

	/**
	 * @return string
	 * @throws moodle_exception
	 */
	public function to(): string {
		global $USER;
		$time = date('YmdHis');

		//TODO разобраться для чего нужно!
		$user_type = 4;

		$uri = "http://www.iprbookshop.ru/autologin";

		$url_params = [
			'domain' => $this->get_data()->domain,
			'uid' => $USER->id,
			'time' => $time,
			'name' => $USER->firstname,
			'lastname' => $USER->lastname,
			'mname' => $USER->middlename,
			'email' => $USER->email,
			'sign' => md5("{$USER->id}{$this->get_data()->token}{$time}"),
			'ut' => $user_type
		];

		return (new moodle_url($uri, $url_params))->out(false);
	}

	/**
	 * @return string
	 */
	public function get_code(): string {
		return "ipr_books";
	}

	/**
	 * @return string
	 * @throws coding_exception
	 */
	public function get_transition_name(): string {
		return get_string('ipr_books_name', 'block_cdo_seamless_transition');
	}

	/**
	 * @return stdClass
	 */
	public function get_other_external_param(): stdClass {
		return new stdClass();
	}
}
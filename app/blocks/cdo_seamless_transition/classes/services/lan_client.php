<?php

namespace block_cdo_seamless_transition\services;

use moodle_exception;
use moodle_url;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\validate_json_response;

global $CFG;

require_once($CFG->libdir . '/filelib.php');

class lan_client {

	/**
	 * @var string
	 */
	private string $token;

	/**
	 * @param string $token
	 * @throws cdo_config_exception
	 */
	public function __construct(string $token) {
		if (empty($token)) {
			throw new cdo_config_exception(1024);
		}
		$this->token = $token;
	}

	/**
	 * @return ?string
	 * @throws \JsonException
	 * @throws moodle_exception
	 */
	public function get_response(): ?string {
		$curl = new \curl();
		$curl->setHeader([
			"X-Auth-Token: {$this->token}",
			'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
			'Accept: application/json'
		]);
		$result = $curl->get($this->get_url()->out(false));
		$result = json_decode($result, false, 512, JSON_THROW_ON_ERROR);
		validate_json_response::get_instance()->set_curl($curl)->set_result($result)->validate();
		return $result->data;
	}

	/**
	 * @return moodle_url
	 * @throws moodle_exception
	 */
	private function get_url(): moodle_url {
		global $USER;

		$request_params = [
			'uid' => $USER->id,
			'time' => date('YmdHi'),
			'fio' => fullname($USER),
			'email' => $USER->email
		];

		return new moodle_url("{$this->get_api_host()}/1.0/security/autologinUrl", $request_params);
	}

	private function get_api_host(): string {
		return 'https://openapi.e.lanbook.com';
	}
}

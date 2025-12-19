<?php

namespace block_cdo_seamless_transition\services;

use moodle_exception;
use moodle_url;
use SimpleXMLElement;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\validate_json_response;

global $CFG;

require_once($CFG->libdir . '/filelib.php');

class consultant_student_client {

	/**
	 * @var string
	 */
	private string $organization;

	/**
	 * @var string
	 */
	private string $contract;

	/**
	 * @param string $organization
	 * @param string $contract
	 * @throws cdo_config_exception
	 */
	public function __construct(string $organization, string $contract) {
		if (empty($organization) || empty($contract)) {
			throw new cdo_config_exception(3006);
		}
		$this->organization = $organization;
		$this->contract = $contract;
	}

	/**
	 * @return ?string
	 * @throws \JsonException
	 * @throws moodle_exception
	 */
	public function get_response(): ?string {
		$curl = new \curl();
		$curl->setHeader(['Content-Type: application/xml']);

		$result = $curl->get($this->get_url()->out(false));
		validate_json_response::get_instance()->set_curl($curl)->validate();
		$xml_link = new SimpleXMLElement($result);
		return trim($xml_link->url);
	}

	/**
	 * @return moodle_url
	 * @throws moodle_exception
	 */
	private function get_url(): moodle_url {
		global $USER;

		$uri_data = "gdaccessdata(shell,seamless_access(";
		$uri_data .= "{$USER->id},";
		$uri_data .= "{$this->organization},";
		$uri_data .= "{$this->contract},";
		$uri_data .= "ru,";
		$uri_data .= base64_encode($USER->lastname) . ",";
		$uri_data .= base64_encode($USER->firstname . " " . $USER->middlename) . ",";
		$uri_data .= ",";
		$uri_data .= "{$USER->email}))";

		$request_params = [
			'usr_data' => $uri_data
		];

		return new moodle_url("{$this->get_api_host()}/cgi-bin/mb4x", $request_params);
	}

	private function get_api_host(): string {
		return 'http://www.studentlibrary.ru';
	}
}

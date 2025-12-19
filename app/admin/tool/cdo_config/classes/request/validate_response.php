<?php

namespace tool_cdo_config\request;

use curl;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\code_errors;

abstract class validate_response {

	protected static self $instance;
	protected array $schema = [];

	/**
	 * @var mixed
	 */
	protected $result = null;

	/**
	 * @var curl
	 */
	protected curl $curl;

	/**
	 * @var mixed
	 */
	protected $data;

	/**
	 * @param int $code
	 * @return void
	 * @throws cdo_config_exception
	 */
	protected function show_default_message_error(int $code): void {

        $message = code_errors::get_msg_by_code($code);
		if ($message !== "") {
			throw new cdo_config_exception(0, $message, true);
		}
	}

	/**
	 * @return mixed
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * @param $result
	 * @return validate_response
	 */
	public function set_result($result): validate_response {
		$this->result = $result;
		return $this;
	}

	/**
	 * @param curl $curl
	 * @return validate_response
	 */
	public function set_curl(curl $curl): validate_response {
		$this->curl = $curl;
		return $this;
	}

	abstract public static function get_instance(): validate_response;

	abstract public function validate(): validate_response;
}

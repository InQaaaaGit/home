<?php

namespace tool_cdo_config\request;

use JsonException;
use tool_cdo_config\exceptions\cdo_config_exception;

class validate_json_response extends validate_response {

	public static function get_instance(): validate_response {
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @return $this
	 * @throws cdo_config_exception
	 */
	public function validate($is_mock = false, $not_json = false): validate_response {
        // for mocked JSON no need curl
        if ($not_json) {
            $this->data = $this->result; return $this;
        }
        if (!$is_mock) {
            if (isset($this->curl) && array_key_exists('http_code', (array)$this->curl->get_info())) {
                $http_code = (int)$this->curl->get_info()['http_code'];

                if ($http_code !== 200) {
                    $this->show_default_message_error(
                        cdo_config_exception::$prefix_errors_num_keys['system_curl'] . $http_code);
                }
            }

            if (!empty($this->curl->error)) {
                throw new cdo_config_exception(1002, $this->curl->error);
            }
        }
		if (isset($this->result)) {
			try {
				$this->data = json_decode(trim(preg_replace('/\s\s+/', ' ', $this->result)), false, 512, JSON_THROW_ON_ERROR);
				/*if (is_object($this->data) && property_exists($this->data, 'code') && $this->data->code !== 200) {
                    //TODO will be new prefix for code error from 1c . $this->data->code
                    throw new cdo_config_exception($this->data->code);
				}*/
			} catch (JsonException $e) {
				throw new cdo_config_exception(1011);
			}
		}
		return $this;
	}
}

<?php

namespace tool_cdo_config;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\handler;
use tool_cdo_config\request\options_build;
use tool_cdo_config\services\request_integration;
use tool_cdo_config\settings\settings;

class di {

	private static self $instance;

	public static function get_instance(): di {
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @param string $code
	 * @return request_integration
	 * @throws cdo_config_exception
	 */
	public function get_request(string $code): request_integration {
		return new request_integration($code);
	}

	/**
	 * @return handler
	 */
	public function get_request_handler(): handler {
		return new handler();
	}

	/**
	 * @return options_build
	 */
	public function get_request_options(): options_build {
		return new options_build();
	}

	/**
	 * @return settings
	 */
	public function get_settings(): settings {
		return settings::get_instance();
	}

}
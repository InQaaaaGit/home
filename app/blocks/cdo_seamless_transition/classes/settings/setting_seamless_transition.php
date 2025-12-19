<?php

namespace block_cdo_seamless_transition\settings;

use moodle_url;
use tool_cdo_config\settings\i_settings;

class setting_seamless_transition implements i_settings {

	public function get_directory_code(): string {
		return "setting_seamless_transition";
	}

	public function get_directory_name(): string {
		return get_string("pluginname", 'block_cdo_seamless_transition');
	}

	public function get_code(): string {
		return "code_setting_seamless_transition";
	}

	public function get_name(): string {
		return get_string("settings_service_list", 'block_cdo_seamless_transition');
	}

	public function get_url_view(): moodle_url {
		return new moodle_url('/blocks/cdo_seamless_transition/pages/settings.php');
	}

	public function get_capability(): array {
		return [
			'moodle/site:config'
		];
	}
}
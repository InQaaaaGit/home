<?php

namespace tool_cdo_config\settings;

use moodle_url;
use tool_cdo_config\configs\main_config;

class setting_integrations implements i_settings {

	public function get_directory_code(): string {
		return "cdo_config_integrations";
	}

	public function get_directory_name(): string {
		return get_string("tool_cdo_config_integrations", main_config::$component);
	}

	public function get_code(): string {
		return "code_cdo_config_integrations";
	}

	public function get_name(): string {
		return get_string("name_cdo_config_integrations", main_config::$component);
	}

	public function get_url_view(): moodle_url {
		return new moodle_url('/admin/tool/cdo_config/pages/settings/integrations/list.php');
	}

	public function get_capability(): array {
		return [
			'moodle/site:config'
		];
	}
}
<?php

namespace local_cdo_certification_sheet\settings;

use moodle_url;
use tool_cdo_config\settings\i_settings;

class setting_certification_sheet implements i_settings {

	public function get_directory_code(): string {
		return "cdo_certification_sheet";
	}

	public function get_directory_name(): string {
		return get_string("pluginname", 'local_cdo_certification_sheet');
	}

	public function get_code(): string {
		return "code_local_cdo_certification_sheet";
	}

	public function get_name(): string {
		return get_string("settings_page", 'local_cdo_certification_sheet');
	}

	public function get_url_view(): moodle_url {
		return new moodle_url('/local/cdo_certification_sheet/pages/settings.php');
	}

	public function get_capability(): array {
		return [
			'moodle/site:config'
		];
	}
}
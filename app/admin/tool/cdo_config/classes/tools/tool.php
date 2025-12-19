<?php

namespace tool_cdo_config\tools;

use context_system;
use moodle_url;
use navigation_node;

class tool {

	public static function access_admin() {
		if (!has_capability('moodle/site:config', context_system::instance())) {
			//TODO change to get_string
			redirect("/my", 'Доступно только администраторам');
		}
	}

	/**
	 * @return navigation_node
	 */
	public static function get_admin_default_navbar(): navigation_node {
		global $PAGE;

		self::access_admin();

		$admin_url = new moodle_url("/admin/search.php");
		$eios_url = new moodle_url("/admin/category.php?category=cdo_config");
		//TODO change to get_string
		return $PAGE->navbar
			->add("Администрирование", $admin_url)
			->add("ЭИОС", $eios_url);
	}

}
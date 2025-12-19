<?php

namespace local_cdo_certification_sheet\services;

defined('MOODLE_INTERNAL') || die();

class tool {

	public static function get_user_id(): int {
        global $USER;
		return $USER->id;
	}
}
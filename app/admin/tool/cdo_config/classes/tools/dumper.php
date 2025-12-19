<?php

namespace tool_cdo_config\tools;

global $CFG;

use Symfony\Component\VarDumper\VarDumper;

require $CFG->dirroot . '/admin/tool/cdo_config/vendor/autoload.php';

class dumper extends VarDumper {

	/**
	 * @param mixed $value
	 * @return void
	 */
	public static function dd($value) {
		self::dump($value);
		die();
	}
}
<?php

namespace local_cdo_ok\output\vuetify;

defined('MOODLE_INTERNAL') || die();

use renderer_base;

class renderable implements \renderable, \templatable {

	public function __construct() {
		// TODO тут обрабатываем входные данные
	}

	public function export_for_template(renderer_base $output): array {
		// TODO тут обрабатываем ответ для рендора
		return [

		];
	}
}


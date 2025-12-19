<?php

namespace local_cdo_certification_sheet\output\info_sheet;

defined('MOODLE_INTERNAL') || die();

use renderer_base;

class renderable implements \renderable, \templatable {

	private array $result;

	public function __construct(array $data) {
		$this->result = $data;
	}

	public function export_for_template(renderer_base $output): array {
		return [
			'items' => $this->result
		];
	}
}
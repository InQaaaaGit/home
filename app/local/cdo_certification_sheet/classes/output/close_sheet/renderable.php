<?php

namespace local_cdo_certification_sheet\output\close_sheet;

defined('MOODLE_INTERNAL') || die();

use renderer_base;
use stdClass;

class renderable implements \renderable, \templatable {

	private stdClass $data;

	/**
	 * @param stdClass $data
	 */
	public function __construct(stdClass $data) {
		$this->data = $data;
	}

	/**
	 * @param renderer_base $output
	 * @return stdClass
	 */
	public function export_for_template(renderer_base $output): stdClass {
		return $this->data;
	}
}
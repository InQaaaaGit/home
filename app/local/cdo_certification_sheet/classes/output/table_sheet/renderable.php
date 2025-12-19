<?php

namespace local_cdo_certification_sheet\output\table_sheet;

defined('MOODLE_INTERNAL') || die();

use renderer_base;
use stdClass;

class renderable implements \renderable, \templatable {

	private string $sheet_guid;
	private array $students;

	public function __construct(array $students, string $sheet_guid) {
		$this->students = $students;
		$this->sheet_guid = $sheet_guid;
	}

	/**
	 * @param renderer_base $output
	 * @return stdClass
	 */
	public function export_for_template(renderer_base $output): stdClass {
		$content = new stdClass();
		$content->students = $this->students;
		$content->sheet_guid = $this->sheet_guid;
		return $content;
	}
}
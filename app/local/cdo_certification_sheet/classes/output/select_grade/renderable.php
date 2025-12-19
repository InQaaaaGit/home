<?php

namespace local_cdo_certification_sheet\output\select_grade;

defined('MOODLE_INTERNAL') || die();

use renderer_base;
use stdClass;

class renderable implements \renderable, \templatable {

	private array $grades;
	private string $student_guid;
	private string $sheet_guid;
	private string $grade_guid;
	private bool $is_edit;

	public function __construct(array $grades, array $guids, bool $is_edit = false) {
		$this->grades = $grades;
		$this->student_guid = $guids['student_guid'];
		$this->sheet_guid = $guids['sheet_guid'];
		$this->grade_guid = $guids['grade_guid'];
		$this->is_edit = $is_edit;
	}

	public function export_for_template(renderer_base $output): stdClass {
		$content = new stdClass();
		$content->grades = $this->grades;
		$content->student_guid = $this->student_guid;
		$content->sheet_guid = $this->sheet_guid;
		$content->grade_guid = $this->grade_guid;
		$content->is_edit = $this->is_edit;
		return $content;
	}
}
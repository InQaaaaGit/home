<?php

namespace local_cdo_certification_sheet\services;

use local_cdo_certification_sheet\DTO\certification_sheet_dto;
use local_cdo_certification_sheet\output\table_sheet\renderable as table_sheet_renderable;
use tool_cdo_config\exceptions\cdo_config_exception;

defined('MOODLE_INTERNAL') || die();

class table_sheet implements i_sheet_component {

	private certification_sheet_dto $sheet;

	public function __construct(certification_sheet_dto $sheet) {
		$this->sheet = $sheet;
	}

	/**
	 * @return string
	 * @throws cdo_config_exception
	 */
	public function build_info(): string {
		global $PAGE;

		$students = [];

		foreach ($this->sheet->students as $student) {
			$property = [
				'student' => (array) $student,
				'system_grades' => $this->sheet->system_grades->to_array(),
				'teachers' => $this->sheet->teachers->to_array(),
				'sheet_type_code' => $this->sheet->type_code,
				'sheet_guid' => $this->sheet->guid
			];
			$students[] = (new grade_element($property))->get_data();
		}

		try {

			$PAGE->requires->js_call_amd('local_cdo_certification_sheet/grades', 'init');
			return $PAGE
				->get_renderer('local_cdo_certification_sheet', 'table_sheet')
				->render(new table_sheet_renderable($students, $this->sheet->guid));
		} catch (\coding_exception $e) {
			throw new cdo_config_exception(2003, $e->getMessage());
		}
	}

}

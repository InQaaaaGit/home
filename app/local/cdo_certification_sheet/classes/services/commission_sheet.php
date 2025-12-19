<?php

namespace local_cdo_certification_sheet\services;

use local_cdo_certification_sheet\DTO\certification_sheet_dto;
use local_cdo_certification_sheet\DTO\teacher_certification_sheet_dto;
use local_cdo_certification_sheet\output\commission_sheet\renderable as commission_sheet_renderable;
use tool_cdo_config\exceptions\cdo_config_exception;

defined('MOODLE_INTERNAL') || die();

class commission_sheet implements i_sheet_component {

	private certification_sheet_dto $sheet;

	public function __construct(certification_sheet_dto $sheet) {
		$this->sheet = $sheet;
	}

	/**
	 * @return teacher_certification_sheet_dto[]
	 */
	protected function get_teachers(): array {
		return $this->sheet->teachers->all();
	}

	/**
	 * @return string
	 * @throws cdo_config_exception
	 */
	public function build_info(): string {
		global $PAGE, $USER;
		//TODO изменить на проде на $USER->id
		$user_id = $USER->id;

		if (list_sheet::is_chairman_sheet() !== $this->sheet->type_code) {
			return "";
		}
		$result = [];
		foreach ($this->get_teachers() as $teacher) {
			if (!(int) $teacher->chairman) {
				$result[] = array_merge(
					(array) $teacher,
					['is_current' => (int) $teacher->user_id === (int) $user_id]
				);
			}
		}
		try {
			$PAGE->requires->js_call_amd('local_cdo_certification_sheet/agreed', 'init');
			return $PAGE
				->get_renderer('local_cdo_certification_sheet', 'commission_sheet')
				->render(new commission_sheet_renderable($result, $this->sheet->guid));
		} catch (\coding_exception $e) {
			throw new cdo_config_exception(2003, $e->getMessage());
		}
	}
}

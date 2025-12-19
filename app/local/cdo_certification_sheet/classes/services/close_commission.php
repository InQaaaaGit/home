<?php

namespace local_cdo_certification_sheet\services;

use local_cdo_certification_sheet\DTO\certification_sheet_dto;
use local_cdo_certification_sheet\DTO\teacher_certification_sheet_dto;
use local_cdo_certification_sheet\output\close_sheet\renderable as close_sheet_renderable;
use stdClass;
use tool_cdo_config\exceptions\cdo_config_exception;

defined('MOODLE_INTERNAL') || die();

class close_commission implements i_sheet_component {
	private certification_sheet_dto $sheet;

	public function __construct(certification_sheet_dto $sheet) {
		$this->sheet = $sheet;
	}

	/**
	 * @return teacher_certification_sheet_dto[]
	 */
	private function get_teachers(): array {
		return $this->sheet->teachers->all();
	}

	/**
	 * @return string
	 * @throws cdo_config_exception
	 */
	public function build_info(): string {
		global $PAGE;
		try {
			$PAGE->requires->js_call_amd('local_cdo_certification_sheet/close', 'init');
			return $PAGE
				->get_renderer('local_cdo_certification_sheet', 'close_sheet')
				->render(new close_sheet_renderable($this->build_close_button()));
		} catch (\coding_exception $e) {
			throw new cdo_config_exception(2003, $e->getMessage());
		}
	}

	private function build_close_button(): stdClass {
		$close_parameters = new stdClass();
		$close_parameters->sheet_guid = $this->sheet->guid;
		$close_parameters->show_close = false;
		$close_parameters->show_reload = false;
		//Ведомость с комиссией
		if (list_sheet::is_chairman_sheet() === $this->sheet->type_code) {
			[$agreed, $is_chairman] = $this->check_chairman();
			if ($is_chairman) {
				if ($agreed) {
					$close_parameters->show_close = true;
				} else {
					$close_parameters->show_reload = true;
				}
			}
		} else {
			//Это учитель
			foreach ($this->get_teachers() as $teacher) {
				if (tool::get_user_id() === (int) $teacher->user_id) {
					$close_parameters->show_close = true;
					break;
				}
			}
		}

		return $close_parameters;
	}

	private function check_chairman(): array {
		//Все ли (кроме председателя) дали согласие с оценками
		$agreed = true;
		foreach ($this->get_teachers() as $teacher) {
			if (!(int) $teacher->chairman && !(int) $teacher->agreed) {
				$agreed = false;
			}
		}
		//Это председатель
		$is_chairman = false;
		foreach ($this->get_teachers() as $teacher) {
			if ((int) $teacher->chairman && tool::get_user_id() === (int) $teacher->user_id) {
				$is_chairman = true;
				break;
			}
		}
		return [$agreed, $is_chairman];
	}
}

<?php

namespace local_cdo_certification_sheet\output\sheet_list;

defined('MOODLE_INTERNAL') || die();

use coding_exception;
use local_cdo_certification_sheet\services\list_sheet;
use renderer_base;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\response_dto;

class renderable implements \renderable, \templatable {

	private string $error = "";

	private list_sheet $list_sheet;

	public function __construct() {
		try {
			$this->list_sheet = new list_sheet($this->get_certification_sheet()->all());
		} catch (cdo_config_exception $e) {
			$this->error = $e->getMessage();
		}
	}

	/**
	 * @return response_dto
	 * @throws cdo_config_exception
	 */
	private function get_certification_sheet(): response_dto {
        global $USER;
		$options = di::get_instance()->get_request_options();
		//TODO изменить на проде на $USER->id
		$options->set_properties(["user_id" => $USER->id]);
		return di::get_instance()
			->get_request("get_list_sheet")
			->request($options)
			->get_request_result();
	}

	/**
	 * @param renderer_base $output
	 * @return array
	 * @throws coding_exception
	 * @throws cdo_config_exception
	 */
	public function export_for_template(renderer_base $output): array {
		return $this->list_sheet->get_data();
	}
}
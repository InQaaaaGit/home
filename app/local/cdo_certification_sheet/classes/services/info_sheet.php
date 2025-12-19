<?php

namespace local_cdo_certification_sheet\services;

use local_cdo_certification_sheet\DTO\certification_sheet_dto;
use local_cdo_certification_sheet\output\info_sheet\renderable as info_sheet_renderable;
use tool_cdo_config\exceptions\cdo_config_exception;

defined('MOODLE_INTERNAL') || die();

class info_sheet implements i_sheet_component{

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

		$result = [];

		foreach ($this->sheet as $key => $item) {
			if (in_array($key, $this->get_need_fields(), true)) {
				$result[] = [
					'name' => $this->get_string($key),
					'value' => $item
				];
			}
		}

		try {
			return $PAGE
				->get_renderer('local_cdo_certification_sheet', 'info_sheet')
				->render(new info_sheet_renderable($result));
		} catch (\coding_exception $e) {
			throw new cdo_config_exception(2003, $e->getMessage());
		}
	}


	/**
	 * @param string $name_field
	 * @return string
	 */
	private function get_string(string $name_field): string {
		$_result = "sheet_{$name_field}";
		$string_manager = get_string_manager();
		if (!$string_manager) {
			return $_result;
		}
		if (!$string_manager->string_exists("sheet_{$name_field}", 'local_cdo_certification_sheet')) {
			return $_result;
		}
		return $string_manager->get_string("sheet_{$name_field}", 'local_cdo_certification_sheet');
	}

	private function get_need_fields(): array {
		return [
			'name_plan',
			'group',
			'profile',
			'semester',
			'division',
			'form_education',
			'level_education',
			'specialty',
			'course',
			'guid',
			'discipline',
			'type_control',
			'name_sheet',
			'type',
			'type_code'
		];
	}
}

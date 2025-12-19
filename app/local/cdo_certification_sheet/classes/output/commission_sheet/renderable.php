<?php

namespace local_cdo_certification_sheet\output\commission_sheet;

defined('MOODLE_INTERNAL') || die();

use coding_exception;
use renderer_base;

class renderable implements \renderable, \templatable {

	private array $result;
	private string $sheet_guid;

	public function __construct(array $data, string $sheet_guid) {
		$this->result = $data;
		$this->sheet_guid = $sheet_guid;
	}

	/**
	 * @param renderer_base $output
	 * @return array
	 * @throws coding_exception
	 */
	public function export_for_template(renderer_base $output): array {
		return [
			'items' => $this->result,
			'sheet_guid' => $this->sheet_guid,
			'agreed_message_yes' => get_string(
				'commission_sheet_agreed_message_yes',
				'local_cdo_certification_sheet'
			),
			'agreed_message_no' => get_string(
				'commission_sheet_agreed_message_no',
				'local_cdo_certification_sheet'
			)
		];
	}
}
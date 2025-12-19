<?php

namespace local_cdo_certification_sheet\services;

use local_cdo_certification_sheet\DTO\certification_sheet_dto;
use tool_cdo_config\exceptions\cdo_config_exception;

defined('MOODLE_INTERNAL') || die();

class single_sheet {

	public certification_sheet_dto $sheet;
	private bool $active_tab = false;

	/**
	 * @return certification_sheet_dto
	 */
	public function get_sheet(): certification_sheet_dto {
		return $this->sheet;
	}

	/**
	 * @return bool
	 */
	public function get_active_tab(): bool {
		return $this->active_tab;
	}

	/**
	 * @param bool $active_tab
	 * @return void
	 */
	public function set_active_tab(bool $active_tab): void {
		$this->active_tab = $active_tab;
	}

	/**
	 * @param certification_sheet_dto $sheet
	 */
	public function __construct(certification_sheet_dto $sheet) {
		$this->sheet = $sheet;
	}

	/**
	 * @return table_sheet
	 */
	public function get_table(): i_sheet_component {
		return new table_sheet($this->get_sheet());
	}

	/**
	 * @return info_sheet
	 */
	public function get_info(): i_sheet_component {
		return new info_sheet($this->get_sheet());
	}

	/**
	 * @return commission_sheet
	 */
	public function get_commission(): i_sheet_component {
		return new commission_sheet($this->get_sheet());
	}

	/**
	 * @return close_commission
	 */
	public function get_close(): i_sheet_component {
		return new close_commission($this->get_sheet());
	}

	/**
	 * @return array
	 * @throws cdo_config_exception
	 */
	public function get_data(): array {
		return [
			'table' => $this->get_table()->build_info(),
			'info' => $this->get_info()->build_info(),
			'commission' => $this->get_commission()->build_info(),
			'close' => $this->get_close()->build_info(),
			'active_tab' => $this->active_tab,
			'guid' => $this->sheet->guid,
			'name_sheet' => $this->sheet->name_sheet
		];
	}

}
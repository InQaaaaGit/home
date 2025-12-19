<?php

namespace local_cdo_certification_sheet\services;

use bootstrap_renderer;
use coding_exception;
use local_cdo_certification_sheet\DTO\certification_sheet_dto;
use local_cdo_certification_sheet\models\setting_model;
use tool_cdo_config\exceptions\cdo_config_exception;

defined('MOODLE_INTERNAL') || die();

class list_sheet {

	/**
	 * @var single_sheet[]
	 */
	private array $list_sheet = [];

	private ?string $active_sheet;

	/**
	 * @param certification_sheet_dto[] $data
	 */
	public function __construct(array $data) {

		foreach ($data as $item) {
			$this->list_sheet[$item->guid] = new single_sheet($item);
		}

		try {
			$this->active_sheet = optional_param('guid', null, PARAM_TEXT);
		} catch (\coding_exception $e) {
			$this->active_sheet = null;
		}

		$this->set_active_sheet();

	}

	public function get_active_sheet(): ?string {
		return $this->active_sheet;
	}

	public function get_list_sheet(): array {
		return $this->list_sheet;
	}

	public function get_is_empty(): bool {
		return empty($this->list_sheet);
	}

	/**
	 * @return string
	 * @throws coding_exception
	 */
	public function get_is_empty_html(): string {
		return bootstrap_renderer::early_notification(
			get_string('list_sheet_not_found_open_sheet', 'local_cdo_certification_sheet'),
			'alert alert-warning');
	}

	/**
	 * @return string
	 * @throws cdo_config_exception
	 */
	public static function is_chairman_sheet(): string {
		return setting_model::get_code();
	}

	protected function set_active_sheet(): void {
		$active_key = "";
		foreach ($this->get_list_sheet() as $key => $item) {
			$this->get_list_sheet()[$key]->set_active_tab(false);
			if ($active_key === "") {
				$this->get_list_sheet()[$key]->set_active_tab(true);
				$active_key = $key;
			}

			if ($this->get_list_sheet()[$key]->sheet->guid === $this->active_sheet) {
				$this->get_list_sheet()[$key]->set_active_tab(true);
				break;
			}
		}
	}

	/**
	 * @return array
	 * @throws cdo_config_exception
	 * @throws coding_exception
	 */
	public function get_data(): array {
		$result = [];
		foreach ($this->list_sheet as $item) {
			$result[] = $item->get_data();
		}

		return [
			'items' => $result,
			'error' => false,
			'error_message' => "",
			'is_empty' => $this->get_is_empty(),
			'is_empty_html' => $this->get_is_empty_html()
		];
	}
}
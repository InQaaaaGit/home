<?php

namespace block_cdo_seamless_transition\transitions;

use block_cdo_seamless_transition\models\setting_model;
use stdClass;

abstract class transition implements i_transition {

	/**
	 * @var bool
	 */
	private bool $active = false;
	/**
	 * @var setting_model
	 */
	private setting_model $setting_model;
	/**
	 * @var stdClass
	 */
	private stdClass $data;
	/**
	 * @var array
	 */
	private array $raw_data;

	public function __construct() {
		$this->data = new stdClass();
		$this->setting_model = new setting_model();
		$this->raw_data = $this->setting_model->get_by_code($this->get_code());
		$this->set_data();
	}

	protected function get_data(): stdClass {
		return $this->data;
	}

	public function is_active(): bool {
		return $this->active;
	}

	protected function ajax_method(): string {
		return 'get_seamless_transition';
	}

	/**
	 * @return external_data_transition
	 * @throws \JsonException
	 */
	public function get_external_data(): external_data_transition {
		return new external_data_transition(
			$this->ajax_method(),
			$this->get_code(),
			$this->get_other_external_param()
		);
	}

	protected function set_data(): void {
		foreach ($this->raw_data as $item) {
			$key = str_replace("{$this->get_code()}_", "", $item->field);
			if ($key === "show") {
				$this->active = (bool) $item->value;
				continue;
			}
			$this->data->{$key} = $this->setting_model->crypt($item->field, $item->value, true);
		}
	}

	abstract protected function get_other_external_param(): stdClass;
}
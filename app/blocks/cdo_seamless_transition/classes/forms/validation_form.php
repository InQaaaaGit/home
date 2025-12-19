<?php

namespace block_cdo_seamless_transition\forms;

class validation_form {

	private array $raw_data;
	private array $data;

	public function __construct($data) {
		$this->raw_data = $data;
	}

	public function get_data(): array {
		return $this->data;
	}

	public function valid(): array {
		$result = [];
		$this->build_full_data();

		foreach ($this->data as $key => $datum) {
			foreach ($datum as $_key => $item) {
				if ($key !== 'show' && $item === "") {
					$result["{$key}_{$_key}"] = get_string('required_param', 'block_cdo_seamless_transition');
				}
			}
		}

		return $result;
	}

	private function build_full_data(): void {
		$keys = $this->get_show_items();
		foreach ($keys as $key) {
			foreach ($this->raw_data as $_key => $item) {
				if (strpos($_key, $key) !== false) {
					$this->data[$key][str_replace("{$key}_", '', $_key)] = $item;
				}
			}
		}
	}

	private function get_show_items(): array {
		$keys = [];
		foreach ($this->raw_data as $key => $item) {
			if (strpos($key, '_show') !== false) {
				$keys[] = str_replace('_show', '', $key);
			}
		}
		return $keys;
	}
}
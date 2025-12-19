<?php

namespace tool_cdo_config\models;


use dml_exception;
use tool_cdo_config\exceptions\cdo_config_exception;

class cdo_config {

	private static string $table = 'cdo_config';

	private static self $instance;

	private object $data;
	private object $old_data;

	protected array $field = [

	];

	protected array $empty_field = [
		"no_auth" => 0,
		"auth_token" => 0,
		"login" => null,
		"password" => null,
		"type_token" => null,
		"token" => null,
		"use_mock" => 0,
		"mock" => null,
        "port" => ''
	];

	protected array $ignore_field = ['action', 'submitbutton'];

	/**
	 * @param string $code
	 * @return bool
	 * @throws cdo_config_exception
	 */
	public static function check_code_uniq(string $code): bool {
		global $DB;
		try {
			$count = $DB->count_records(self::$table, ['code' => $code]);
            return ($count === 0 || $count === 1);
		} catch (dml_exception $e) {
			throw new cdo_config_exception(2001, $e->getMessage());
		}
	}

	public static function get_instance(): cdo_config {
		if (!isset(self::$instance)){
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @return int
	 * @throws cdo_config_exception
	 */
	public function create(): int {
		global $DB;
		try {
			$this->set_time_created();
			$this->set_time_modified();
			return $DB->insert_record(self::$table, $this->get_data());
		} catch (\dml_exception $e) {
			throw new cdo_config_exception(2001, $e->getMessage());
		}
	}

	/**
	 * @return int
	 * @throws cdo_config_exception
	 */
	public function update(): int {
		global $DB;
		try {
			$this->set_time_modified();
			return $DB->update_record(self::$table, $this->get_data());
		} catch (\dml_exception $e) {
			throw new cdo_config_exception(2001, $e->getMessage());
		}
	}

	/**
	 * @return int
	 * @throws cdo_config_exception
	 */
	public function save(): int {

		if (!self::check_code_uniq($this->get_data()->code)) {
			return 0;
		}

		if (isset($this->old_data)){
			return $this->update();
		}
		return $this->create();
	}

	/**
	 * @return array
	 * @throws cdo_config_exception
	 */
	public function get_list(): array {
		global $DB;
		try {
			return $DB->get_records(self::$table);
		} catch (dml_exception $e) {
			throw new cdo_config_exception(2001, $e->getMessage());
		}
	}

	/**
	 * @param int $id
	 * @return object|bool
	 * @throws cdo_config_exception
	 */
	public function get_detail(int $id) {
		global $DB;
		try {
			return $DB->get_record(self::$table, ['id' => $id]);
		} catch (dml_exception $e) {
			throw new cdo_config_exception(2001, $e->getMessage());
		}
	}

	/**
	 * @param string $code
	 * @return object|bool
	 * @throws cdo_config_exception
	 */
	public function get_detail_by_code(string $code) {
		global $DB;
		try {
			return $DB->get_record(self::$table, ['code' => $code]);
		} catch (dml_exception $e) {
			throw new cdo_config_exception(2001, $e->getMessage());
		}
	}

	/**
	 * @param int $id
	 * @return bool
	 * @throws cdo_config_exception
	 */
	public function delete(int $id): bool {
		global $DB;
		try {
			return $DB->delete_records(self::$table, ['id' => $id]);
		} catch (dml_exception $e) {
			throw new cdo_config_exception(2001, $e->getMessage());
		}
	}

	/**
	 * @param object $data
	 * @return $this
	 * @throws cdo_config_exception
	 * TODO доработать поведение модели!
	 */
	public function set_data(object $data): cdo_config {
		global $DB;
		$this->data = $this->rebuild_data($data);
		if (property_exists($this->data, 'id') && $this->data->id !== ''){
			try {
				$this->old_data = $DB->get_record(self::$table, ['id' => $this->data->id]);
			} catch (dml_exception $e) {
				throw new cdo_config_exception(2001, $e->getMessage());
			}
		}
		return $this;
	}

	/**
	 * @return object
	 */
	public function get_data(): object {
		return $this->data;
	}

	/**
	 * @return object
	 */
	public function get_old_data(): object {
		return $this->old_data;
	}

	protected function rebuild_data(object $data): object {
		foreach ($this->empty_field as $key => $item) {
			if (!property_exists($data, $key)){
				$data->{$key} = $item;
			}
		}
		foreach ($this->ignore_field as $item) {
			if (property_exists($data, $item)){
				unset($data->{$item});
			}
		}
		return $data;
	}

	/**
	 * @return void
	 */
	protected function set_time_created(): void {
		$this->data->timecreated = time();
	}

	/**
	 * @return void
	 */
	protected function set_time_modified(): void {
		$this->data->timemodified = time();
	}
}

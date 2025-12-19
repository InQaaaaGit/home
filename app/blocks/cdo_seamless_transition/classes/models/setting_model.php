<?php

namespace block_cdo_seamless_transition\models;

use coding_exception;
use dml_exception;
use JsonException;
use moodle_database;
use stdClass;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\tools\cryptographer;

class setting_model {

	private string $crypt_key = "G9Y5SGxBwMPU2rXNC71HQHsuWmNDlRhJ";
	private cryptographer $cryptographer;
	private string $table = "cdo_seamless_transition";
	private array $data;

	public function __construct() {
		$this->cryptographer = new cryptographer($this->crypt_key);
	}

	/**
	 * @param array $data
	 * @return setting_model
	 * @throws JsonException
	 * @throws cdo_config_exception
	 */
	public function set_data(array $data): setting_model {
		foreach ($data as $key => $item) {
			foreach ($item as $_key => $value) {
				if ($_key !== 'show') {
					$value = $this->cryptographer->encrypt($value);
				}
				$object = new stdClass();
				$object->field = "{$key}_{$_key}";
				$object->value = $value;
				$this->data[] = $object;
			}
		}
		return $this;
	}

	/**
	 * @return void
	 * @throws cdo_config_exception
	 */
	public function save(): void {
		try {
			$this->set_deleted();
			$this->get_db()->insert_records($this->table, $this->data);
		} catch (coding_exception|cdo_config_exception $e) {
			throw new cdo_config_exception(2003, $e->getMessage());
		} catch (dml_exception $e) {
            throw new cdo_config_exception(2001, $e->getMessage());
        }
	}

	public function get_by_code(string $code, ?int $deleted = null) {

		if(is_null($deleted)) {
			$str_deleted = "`deleted` IS NULL";
		} else {
			$str_deleted = "`deleted` = {$deleted}";
		}

		$sql = sprintf(
			"SELECT * FROM %s WHERE `field` like %s AND %s",
			"mdl_{$this->table}",
			"'%{$code}%'",
			$str_deleted
		);
		return $this->get_db()->get_records_sql($sql);
	}

	/**
	 * @param int|null $deleted
	 * @return array
	 * @throws cdo_config_exception
	 */
	public function get_list(?int $deleted = null): array {
		try {
			return $this->get_db()->get_records($this->table, ['deleted' => $deleted]);
		} catch (dml_exception $e) {
			throw new cdo_config_exception(2001, $e->getMessage());
		}
	}

	/**
	 * @return array
	 * @throws cdo_config_exception
	 */
	public function get_list_deleted(): array {
		return $this->get_list(1);
	}

	/**
	 * @return array
	 * @throws cdo_config_exception
	 */
	public function get_list_to_form() {
		$result = [];
		foreach ($this->get_list() as $item) {
			$result[$item->field] = $this->crypt($item->field, $item->value, true);
		}
		return $result;
	}

	private function get_db(): moodle_database {
		global $DB;
		return $DB;
	}

	/**
	 * @return void
	 * @throws cdo_config_exception
	 * @throws dml_exception
	 */
	private function set_deleted(): void {
		$delete = $this->get_list_deleted();
		foreach ($this->get_list() as $item) {
			$item->deleted = 1;
			$this->get_db()->update_record($this->table, $item);
		}
		if (count($delete)) {
			$this->remove_old_deleted($delete);
		}
	}

	/**
	 * @param array $items
	 * @return void
	 * @throws dml_exception
	 */
	private function remove_old_deleted(array $items): void {
		foreach (array_keys($items) as $item) {
			$this->get_db()->delete_records($this->table, ['id' => $item]);
		}
	}

	public function crypt(string $field, string $value, bool $decrypt = false) {
		if (strpos($field, '_show') !== false) {
			return $value;
		}
		if ($decrypt) {
			return $this->cryptographer->decrypt($value);
		}
		return $this->cryptographer->encrypt($value);
	}

}

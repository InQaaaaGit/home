<?php

namespace local_cdo_certification_sheet\models;

use stdClass;
use tool_cdo_config\exceptions\cdo_config_exception;

class setting_model {

	private static string $file_name = "setting.json";
	private static string $default_code = "000000003";

	/**
	 * @return string
	 * @throws cdo_config_exception
	 */
	public static function get_code(): string {
		return self::read_code();
	}

	/**
	 * @param string $code
	 * @return bool
	 * @throws cdo_config_exception
	 */
	public static function save_code(string $code): bool {
		return self::write_file(self::build_data($code));
	}

	/**
	 * @return string
	 * @throws cdo_config_exception
	 */
	private static function read_code(): string {
		if (!self::check_file()) {
			self::write_file(self::build_data());
		}

		if (!self::check_file()) {
			throw new cdo_config_exception(2006);
		}

		$raw_data = file_get_contents(__DIR__ . "/" . self::$file_name);
		try {
			$data = json_decode($raw_data, false, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			throw new cdo_config_exception(1012);
		}
		if (property_exists($data, 'code')) {
			return $data->code;
		}
		return self::$default_code;
	}

	/**
	 * @param stdClass $data
	 * @return bool
	 * @throws cdo_config_exception
	 */
	private static function write_file(stdClass $data): bool {
		self::clear_file();
		try {
			return (bool)file_put_contents(self::get_file_path(), json_encode($data, JSON_THROW_ON_ERROR));
		} catch (\JsonException $e) {
			throw new cdo_config_exception(1012);
		}
	}

	/**
	 * @return bool
	 */
	private static function check_file(): bool {
		return is_file(__DIR__ . "/" . self::$file_name);
	}

	/**
	 * @param string $code
	 * @return stdClass
	 */
	private static function build_data(string $code = ""): stdClass {
		if ($code === "") {
			$code = self::$default_code;
		}
		$default_data = new stdClass();
		$default_data->code = $code;
		return $default_data;
	}

	/**
	 * @return void
	 */
	private static function clear_file(): void {
		file_put_contents(self::get_file_path(), '');
	}

	/**
	 * @return string
	 */
	private static function get_file_path(): string {
		return __DIR__ . "/" . self::$file_name;
	}
}

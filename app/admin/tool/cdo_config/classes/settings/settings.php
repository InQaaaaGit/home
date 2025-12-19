<?php

namespace tool_cdo_config\settings;

use admin_category;
use admin_externalpage;
use admin_root;
use coding_exception;
use context_system;
use ReflectionClass;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\supported_plugins\plugins;

class settings {

	private static settings $instance;

	private static bool $built_before = false;

	public const ROOT_CATEGORY = 'cdo_config';

	private admin_root $admin_setting;

	private bool $hase_config = false;
	private array $setting_list = [];

	public static function get_instance(): settings {
		if (!isset(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {

	}

	/**
	 * @return void
	 * @throws cdo_config_exception
	 */
	protected function get_hase_config(): void {
		try {
			$this->hase_config = has_capability('moodle/site:config', context_system::instance());
		} catch (\dml_exception $e) {
			throw new cdo_config_exception(2001, $e->getMessage());
		} catch (coding_exception $e) {
            throw new cdo_config_exception(2003, $e->getMessage());
        }
	}

	protected function get_admin_setting(): void {
		global $ADMIN;
		$this->admin_setting = $ADMIN;
	}

	/**
	 * @return bool
	 * @description Проверяем повторный вызов настроек
	 */
	private function is_built_before(): bool {
		if (self::$built_before) {
			return false;
		}
		self::$built_before = true;
		return self::$built_before;
	}

	/**
	 * @return void
	 * @throws cdo_config_exception
	 */
	public function settings_builder(): void {
		if ($this->is_built_before()) {
			plugins::get_instances();
			$this->get_all_settings_class();
			$this->get_hase_config();
			$this->get_admin_setting();
			if ($this->hase_config) {
				$this->build_root_directory();
				$this->build_children_directory();
			}
		}
	}

	/**
	 * @return void
	 * @throws cdo_config_exception
	 */
	private function get_all_settings_class(): void {
		foreach (plugins::get_instances()->get_plugins() as $plugin) {
			foreach ($plugin->get_classes_settings() as $item) {
				try {
					$reflection = new ReflectionClass($item);
					if (array_key_exists(i_settings::class, $reflection->getInterfaces())) {
						$this->setting_list[] = new $reflection->name();
					}
				} catch (\ReflectionException $e) {
					throw new cdo_config_exception(2004, $e->getMessage());
				}
			}
		}
	}

	/**
	 * @return void
	 * @throws cdo_config_exception
	 */
	private function build_root_directory(): void {
		//Если нет раздела по управлению контентом - создаем
		if (!$this->admin_setting->locate(self::ROOT_CATEGORY, 'root')) {
			try {
				$this->admin_setting->add(
					'root',
					new admin_category(self::ROOT_CATEGORY, $this->get_name_root_category())
				);
			} catch (coding_exception $e) {
				throw new cdo_config_exception(2003, $e->getMessage());
			}
		}
	}

	/**
	 * @return void
	 * @throws cdo_config_exception
	 */
	private function build_children_directory(): void {
		foreach ($this->setting_list as $setting) {
			try {
				$this->check_children_directory($setting);
				$external_page = $this->build_external_page($setting);
				if (!is_null($external_page)) {
					$this->admin_setting->add(
						$setting->get_directory_code(),
						$external_page
					);
				}
			} catch (coding_exception $e) {
				throw new cdo_config_exception(2003, $e->getMessage());
			}

		}
	}

	/**
	 * @param i_settings $setting
	 * @return void
	 * @throws cdo_config_exception
	 */
	private function check_children_directory(i_settings $setting): void {
		if (!$this->admin_setting->locate($setting->get_directory_code(), self::ROOT_CATEGORY)) {
			try {
				$this->admin_setting->add(
					self::ROOT_CATEGORY,
					new admin_category($setting->get_directory_code(), $setting->get_directory_name()));
			} catch (coding_exception $e) {
				throw new cdo_config_exception(2003, $e->getMessage());
			}
		}
	}

	/**
	 * @param i_settings $setting
	 * @return ?admin_externalpage
	 */
	private function build_external_page(i_settings $setting): ?admin_externalpage {
		if (!$this->admin_setting->locate($setting->get_code())) {
			return new admin_externalpage(
				$setting->get_code(),
				$setting->get_name(),
				$setting->get_url_view(),
				$setting->get_capability()
			);
		}
		return null;
	}

	/**
	 * @return string
	 */
	private function get_name_root_category(): string {
		return get_string('root_category', 'tool_cdo_config');
	}
}

<?php

namespace tool_cdo_config\supported_plugins;

use core_plugin_manager;
use tool_cdo_config\configs\main_config;

class plugins {

	/**
	 * @var single_plugin[]
	 */
	private array $plugins = [];

	private static plugins $instance;

	public static function get_instances(): plugins {
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {
		$this->build_plugins_info();
	}

	/**
	 * @return void
	 */
	private function build_plugins_info(): void {
		foreach (main_config::$supported_plugins as $supported_plugin) {
			$_plugin = core_plugin_manager::instance()->get_plugin_info($supported_plugin);
			if ($_plugin && $_plugin->rootdir) {
				$this->plugins[$supported_plugin] = new single_plugin($_plugin);
			}
		}
	}

	/**
	 * @param string $plugin_name
	 * @return bool
	 */
	public function has_install_plugins(string $plugin_name): bool {
		if (array_key_exists($plugin_name, main_config::$alias_supported_plugins)) {
			$plugin_name = main_config::$alias_supported_plugins[$plugin_name];
		}
		return array_key_exists($plugin_name, $this->plugins);
	}

	/**
	 * @param string $plugin_name
	 * @return single_plugin|null
	 */
	public function get_single_plugin(string $plugin_name): ?single_plugin {
		if (array_key_exists($plugin_name, main_config::$alias_supported_plugins)) {
			$plugin_name = main_config::$alias_supported_plugins[$plugin_name];
		}
		return $this->plugins[$plugin_name] ?? null;
	}

	/**
	 * @return single_plugin[]
	 */
	public function get_plugins(): array {
		return $this->plugins;
	}
}
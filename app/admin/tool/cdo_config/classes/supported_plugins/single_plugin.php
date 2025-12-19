<?php

namespace tool_cdo_config\supported_plugins;

use core\plugininfo\base;

class single_plugin {
	private string $name;
	private string $type;
	private string $path;
	private string $real_path;
	private string $namespace;
	private array $classes = [];

	public function __construct(base $parameters) {
		$this->type = $parameters->type;
		$this->name = $parameters->name;
		$this->path = $parameters->get_dir();
		$this->real_path = $parameters->rootdir;
		$this->namespace = "{$this->type}_{$this->name}";
		$this->set_classes();
	}

	private function set_classes(): void {
		if (!is_dir("{$this->real_path}/classes")) {
			return;
		}
		foreach (scandir("{$this->real_path}/classes") as $item) {
			if ($item === '.' || $item === '..') {
				continue;
			}
			$classes = $this->scan_dir("{$this->real_path}/classes/{$item}");
			if ($classes) {
				$this->classes[$item] = $classes;
			}
		}
	}

	private function scan_dir(string $dir): ?array {
		$result = [];
		if (!is_file($dir)) {
			foreach (scandir($dir) as $item) {
				if ($item === '.' || $item === '..') {
					continue;
				}

				$_dir = "{$dir}/{$item}";

				if (is_dir($_dir)) {
					$result = $this->scan_dir($_dir);
				} else if (is_file($_dir) && preg_match('#\/\w+\.php$#', $_dir)) {
					[$_class, $_namespace] = $this->get_file_info($_dir);
					$result[] = $this->namespace . implode("\\", $_namespace) . "\\$_class";
				}
			}
		}
		return $result ?? null;
	}

	private function get_file_info(string $dir): array {
		$file = explode("/", str_replace("{$this->real_path}/classes", '', $dir));

		$_class = "";
		$_namespace = [];

		foreach ($file as $f) {
			if (strpos($f, '.php') !== false) {
				$_class = str_replace(".php", "", $f);
				break;
			}
			$_namespace[] = $f;
		}
		return [$_class, $_namespace];
	}

	/**
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_path(): string {
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function get_real_path(): string {
		return $this->real_path;
	}

	/**
	 * @return string
	 */
	public function get_namespace(): string {
		return $this->namespace;
	}

	/**
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * @return string[]
	 */
	public function get_classes(): array {
		return $this->classes;
	}

	/**
	 * @return array
	 */
	public function get_classes_settings(): array {
		return $this->classes['settings'] ?? [];
	}
}

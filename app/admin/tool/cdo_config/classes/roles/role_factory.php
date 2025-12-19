<?php

namespace tool_cdo_config\roles;

use ReflectionClass;

class role_factory {

	/**
	 * @var ReflectionClass[]
	 */
	private array $classes_role;

	public function __construct() {
		$this->classes_role = $this->get_all_classes_role();
	}

	/**
	 * @return void
	 */
	public function create_roles(): void {
		foreach ($this->classes_role as $class) {
			if (
				$class->implementsInterface(i_single_role::class)
				&& $class->getParentClass()
				&& $class->getParentClass()->getName() === base_role::class
			) {
				//TODO LOG заведение новой роли
				(new $class->name)->create_role();
			}
		}
	}

	/**
	 * @param string $class
	 * @return bool
	 */
	public function has_class_role(string $class): bool {
		return isset($this->classes_role[$class]);
	}

	/**
	 * @param string $class
	 * @return base_role|null
	 */
	public function get_class_role(string $class): ?base_role {
		return $this->classes_role[$class] ? new $this->classes_role[$class]->name() : null;
	}

	/**
	 * @return ReflectionClass[]
	 */
	protected function get_all_classes_role(): array {
		global $CFG;
		$result = [];

		$files = scandir("{$CFG->dirroot}/admin/tool/cdo_config/classes/roles");

		foreach ($files as $file) {
			if (strpos($file, '.php') !== false) {
				$_file = str_replace('.php', '', $file);
				$reflection = new ReflectionClass("tool_cdo_config\\roles\\{$_file}");
				if ($reflection->getParentClass() && $reflection->getParentClass()->getName() === "tool_cdo_config\\roles\\base_role") {
					$result[$_file] = $reflection;
				}
			}
		}
		return $result;
	}

}
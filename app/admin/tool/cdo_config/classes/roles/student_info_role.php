<?php

namespace tool_cdo_config\roles;

use context;
use dml_exception;
use tool_cdo_config\exceptions\cdo_config_exception;


class student_info_role extends base_role implements i_single_role {

	public function get_name_capability(): string {
		return "student_info";
	}

	public function get_ignore_activity_capability(): array {
		return [];
	}

	public function get_role_name(): string {
		return "(ЦДО) Обучающийся";
	}

	public function get_role_shortname(): string {
		return "cdo_student_shortname";
	}

	public function get_role_description(): string {
		return "Стандартная набор прав для обучеющихся";
	}

	/**
	 * @return context
	 * @throws cdo_config_exception
	 */
	public function get_role_context(): context {
		try {
			return \context_system::instance();
		} catch (dml_exception $e) {
			throw new cdo_config_exception(2002);
		}
	}

	/**
	 * @param capability_option $option
	 * @return bool
	 * @throws cdo_config_exception
	 */
	public function assign_capability(capability_option $option): bool {
		try {
			return assign_capability(
				$option->get_capability(),
				$option->get_permission(),
				$this->get_role_id(),
				$this->get_role_context()
            );
		} catch (\coding_exception $e) {
			throw new cdo_config_exception(2003, $e->getMessage());
		}
	}

	/**
	 * @param capability_option $option
	 * @return bool
	 * @throws cdo_config_exception
	 */
	public function un_assign_capability(capability_option $option): bool {
		try {
			return unassign_capability(
				$option->get_capability(),
				$this->get_role_id(),
				$this->get_role_context());
		} catch (\coding_exception $e) {
			throw new cdo_config_exception(2003, $e->getMessage());
		}
	}

	/**
	 * @return int
	 * @throws cdo_config_exception
	 */
	/*protected function get_role_id(): int {
		global $DB;

		$conditions = [
			'name' => $this->get_role_name(),
			'shortname' => $this->get_role_shortname()
		];

		try {
			$role = $DB->get_record('role', $conditions);
			return $role->id ?? 0;
		} catch (dml_exception $e) {
			throw new cdo_config_exception(2001, $e->getMessage());
		}
	}*/
}

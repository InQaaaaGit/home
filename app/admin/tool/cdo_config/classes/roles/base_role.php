<?php

namespace tool_cdo_config\roles;

use coding_exception;
use context;
use dml_exception;
use stdClass;
use tool_cdo_config\exceptions\cdo_config_exception;

abstract class base_role {

	private array $roles;
	private stdClass $role;

	/**
	 * @throws coding_exception
	 */
	public function create_role(): void {
		$this->check_role();
		if (!isset($this->role)) {
			create_role($this->get_role_name(), $this->get_role_shortname(), $this->get_role_description());
			$this->check_role();
		}

		set_role_contextlevels($this->role->id, [$this->get_role_context()->contextlevel]);
	}

	/**
	 * @return void
	 */
	private function check_role(): void {
		$this->roles = get_all_roles($this->get_role_context());
		foreach ($this->roles as $role) {
			if ($role->shortname === $this->get_role_shortname()) {
				$this->role = $role;
			}
		}
	}

	/**
	 * @return string
	 */
	abstract public function get_role_name(): string;

	/**
	 * @return string
	 */
	abstract public function get_role_shortname(): string;

	/**
	 * @return string
	 */
	abstract public function get_role_description(): string;

	/**
	 * @return context
	 */
	abstract public function get_role_context(): context;

	/**
	 * @return int
	 */
	protected function get_role_id(): int {
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
    }

	/**
	 * @param capability_option $option
	 * @return bool
	 */
	abstract public function assign_capability(capability_option $option): bool;

	/**
	 * @param capability_option $option
	 * @return bool
	 */
	abstract public function un_assign_capability(capability_option $option): bool;
}
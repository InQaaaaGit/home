<?php

namespace tool_cdo_config\roles;

class capability_option {

	private string $capability;
	private int $permission;
	private bool $overwrite;

	/**
	 * @return string
	 */
	public function get_capability(): string {
		return $this->capability;
	}

	/**
	 * @return int
	 */
	public function get_permission(): int {
		return $this->permission;
	}

	/**
	 * @return bool
	 */
	public function is_overwrite(): bool {
		return $this->overwrite;
	}

	/**
	 * @param string $_capability
	 * @param int $_permission
	 * @param bool $_overwrite
	 */
	public function __construct(string $_capability, int $_permission, bool $_overwrite = false) {
		$this->capability = $_capability;
		$this->permission = $_permission;
		$this->overwrite = $_overwrite;
	}

}
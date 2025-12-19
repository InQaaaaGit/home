<?php

namespace block_cdo_schedule\dto;

use tool_cdo_config\request\DTO\base_dto;

final class subgroup_dto extends base_dto {

	public array $data;

	/**
	 * @return string
	 */
	protected function get_object_name(): string {
		return "subgroup";
	}

	/**
	 * @param object $data
	 * @return directory_dto
	 */
	public function build(object $data): self {
		$this->data = $data->data ?? [];

		return $this;
	}
}
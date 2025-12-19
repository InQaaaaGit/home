<?php

namespace block_cdo_student_info\dto;

use tool_cdo_config\request\DTO\base_dto;

final class directory_dto extends base_dto {

	public string $id;
	public string $name;
	public ?string $code;
	public ?string $guid;
	public ?string $number;

	/**
	 * @return string
	 */
	protected function get_object_name(): string {
		return "directory";
	}

	/**
	 * @param object $data
	 * @return directory_dto
	 */
	public function build(object $data): self {
		$this->id = $data->id ?? null;
		$this->name = $data->name ?? null;
		$this->code = $data->code ?? null;
		$this->guid = $data->guid ?? null;

		return $this;
	}
}
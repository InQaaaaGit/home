<?php

namespace tool_cdo_config\request\DTO;

final class directory_dto extends base_dto {

	public string $id;
	public string $name;
	public ?string $code;

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
		return $this;
	}
}
<?php

namespace local_cdo_certification_sheet\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class system_grade_certification_sheet_dto extends base_dto {

	public string $id;
	public string $value;
	public string $grade;
	public string $guid;
	public ?string $short_name;
	public string $color;

	public function build(object $data): base_dto {
		$this->id = $data->id ?? null;
		$this->value = $data->value ?? null;
		$this->guid = $data->GUID ?? null;
		$this->grade = $data->grade ?? null;
		$this->short_name = $data->short_name ?? "";
		$this->color = $data->color ?? null;
		return $this;
	}

	protected function get_object_name(): string {
		return "system_grade_certification_sheet";
	}
}
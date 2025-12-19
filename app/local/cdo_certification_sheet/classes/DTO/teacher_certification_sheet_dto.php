<?php

namespace local_cdo_certification_sheet\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class teacher_certification_sheet_dto extends base_dto {

	public string $full_name;
	public string $code;
	public string $guid;
	public ?bool $chairman;
	public ?bool $agreed;
	public string $user_id;

	public function build(object $data): base_dto {
		$this->full_name = $data->FIO ?? null;
		$this->code = $data->CODE ?? null;
		$this->guid = $data->GUID ?? null;
		$this->chairman = $data->chairman ?? false;
		$this->agreed = $data->agreed ?? false;
		$this->user_id = $data->user_id ?? null;
		return $this;
	}

	protected function get_object_name(): string {
		return "teachers_certification_sheet";
	}
}
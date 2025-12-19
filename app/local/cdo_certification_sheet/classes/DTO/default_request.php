<?php

namespace local_cdo_certification_sheet\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class default_request extends base_dto {

	public bool $success;
	public string $error;

	protected function get_object_name(): string {
		return "insert_grade";
	}

	public function build(object $data): base_dto {
		$this->success = $data->success ?? null;
		$this->error = $data->error ?? null;
		return $this;
	}
}
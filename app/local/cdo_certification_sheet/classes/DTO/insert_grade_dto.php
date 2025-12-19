<?php

namespace local_cdo_certification_sheet\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class insert_grade_dto extends base_dto {

	public bool $success;
	public string $error;
	public grade_details_dto $grade;
	public string $note;

	protected function get_object_name(): string {
		return "insert_grade_dto";
	}

	public function build(object $data): base_dto {
		$this->success = $data->success ?? false;
		$this->error = $data->error ?? '';
		$this->grade = new grade_details_dto($data->grade ?? null);
		$this->note = $data->note ?? '';
		return $this;
	}
}
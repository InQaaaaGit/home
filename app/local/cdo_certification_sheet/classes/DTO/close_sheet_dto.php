<?php

namespace local_cdo_certification_sheet\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class close_sheet_dto extends base_dto {

	public bool $execution_result;
	public bool $closed;

	protected function get_object_name(): string {
		return "close_sheet";
	}

	public function build(object $data): base_dto {
		$this->execution_result = $data->execution_result ?? null;
		$this->closed = $data->closed ?? null;
		return $this;
	}
}
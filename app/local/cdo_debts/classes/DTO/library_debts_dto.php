<?php

namespace local_cdo_debts\DTO;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class library_debts_dto extends base_dto {

	public bool $success;
	public string $datestamp;

	public response_dto $data;

	/**
	 * @param object $data
	 * @return base_dto
	 * @throws cdo_config_exception
	 */
	public function build(object $data): base_dto {
		$this->success = $data->success ?? null;
		$this->datestamp = $data->datestamp ?? null;

		$this->data = $data->data
			? response_dto::transform(library_debts_data_dto::class, $data->data)
			: null;

		return $this;
	}

	protected function get_object_name(): string {
		return "library_debts";
	}
}
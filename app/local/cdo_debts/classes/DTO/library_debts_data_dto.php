<?php

namespace local_cdo_debts\DTO;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;

final class library_debts_data_dto extends base_dto {

	public string $doc_id;
	public string $title;
	public string $date_take;
	public string $date_return_plan;
	public string $date_return_fact;
	public string $expired;

	/**
	 * @param object $data
	 * @return base_dto
	 * @throws cdo_config_exception
	 */
	public function build(object $data): base_dto {
		$this->doc_id = $data->doc_id ?? null;
		$this->title = $data->title ?? null;
		$this->date_take = $data->date_take ?? null;
		$this->date_return_plan = $data->date_return_plan ?? null;
		$this->date_return_fact = $data->date_return_fact ?? null;
		$this->expired = $data->expired ?? null;

		return $this;
	}

	protected function get_object_name(): string {
		return "library_debts";
	}
}
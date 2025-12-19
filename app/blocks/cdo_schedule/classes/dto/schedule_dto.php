<?php

namespace block_cdo_schedule\dto;

use coding_exception;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class schedule_dto extends base_dto {

	public string $date;
	public string $order;
	public ?response_dto $items;
    public ?string $date_1c;

    /**
     * @param object $data
     * @return $this
     * @throws coding_exception
     * @throws cdo_type_response_exception
     */
	public function build(object $data): self {
		$this->date = $data->date ?? null;
		$this->date_1c = $data->date_1c;
		$this->order = $data->order ?? null;
		$this->items = $data->items
			? response_dto::transform(schedule_item_dto::class, $data->items)
			: null;
		return $this;
	}

	/**
	 * @return string
	 */
	protected function get_object_name(): string {
		return "schedule";
	}
}

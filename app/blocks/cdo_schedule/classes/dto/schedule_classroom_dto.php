<?php

namespace block_cdo_schedule\dto;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class schedule_classroom_dto extends base_dto
{

    public response_dto $room;
    public response_dto $building;

    /**
     * @param object $data
     * @return $this
     * @throws cdo_config_exception
     */
    public function build(object $data): self
    {
        $this->room = $data->room
            ? response_dto::transform(directory_dto::class, $data->room)
            : null;
        $this->building = $data->building
            ? response_dto::transform(directory_dto::class, $data->building)
            : null;
        return $this;
    }

    /**
     * @return string
     */
    protected function get_object_name(): string
    {
        return "schedule_classroom";
    }
}
<?php

namespace local_cdo_mto\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class data_dto extends base_dto {

    public ?response_dto $building;
    public ?response_dto $room;

    protected function get_object_name(): string
    {
        return "data_dto";
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->building = $data->building
            ? response_dto::transform(data_dto::class, $data->building)
            : null;
        $this->room = $data->room
            ? response_dto::transform(data_dto::class, $data->room)
            : null;
        return $this;
    }
}
<?php

namespace local_cdo_mto\DTO\room;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class data_dto extends base_dto {

    public $room;

    protected function get_object_name(): string
    {
        return "data_dto";
    }

    public function build(object $data): self
    {
      $this->room = $data->room
            ? response_dto::transform(room_dto::class, $data->room)
            : null;
        return $this;
    }
}

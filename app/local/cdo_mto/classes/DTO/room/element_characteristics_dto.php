<?php

namespace local_cdo_mto\DTO\room;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class element_characteristics_dto extends base_dto {
    public $room_capacity;
    public $room_area;
    public $room_special;
    public $room_number;
    public $room_technumber;
    public $room_description;
    public $room_equipment;
    public $room_type;

    protected function get_object_name(): string
    {
        return "element_characteristics_dto";
    }

    public function build(object $data): self
    {
        $this->room_capacity = $data->room_capacity
          ? response_dto::transform(room_characteristics_dto::class, $data->room_capacity)
          : ['value', 'quantity'];
        $this->room_area = $data->room_area
          ? response_dto::transform(room_characteristics_dto::class, $data->room_area)
          : ['value', 'quantity'];
        $this->room_special = $data->room_special
          ? response_dto::transform(room_characteristics_dto::class, $data->room_special)
          : ['value', 'quantity'];
        $this->room_number = $data->room_number
          ? response_dto::transform(room_characteristics_dto::class, $data->room_number)
          : ['value', 'quantity'];
        $this->room_technumber = $data->room_technumber
          ? response_dto::transform(room_characteristics_dto::class, $data->room_technumber)
          : ['value', 'quantity'];
        $this->room_description = $data->room_description
          ? response_dto::transform(room_characteristics_dto::class, $data->room_description)
          : ['value', 'quantity'];
        $this->room_equipment = $data->room_equipment
          ? response_dto::transform(room_characteristics_dto::class, $data->room_equipment)
          : ['value', 'quantity'];
        $this->room_type = $data->room_type
          ? response_dto::transform(room_characteristics_dto::class, $data->room_type)
          : ['value', 'quantity'];
        return $this;
    }
}

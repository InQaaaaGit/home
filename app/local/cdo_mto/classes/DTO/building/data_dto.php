<?php

namespace local_cdo_mto\DTO\building;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class data_dto extends base_dto {

    public $building;

    protected function get_object_name(): string
    {
        return "data_dto";
    }

    public function build(object $data): self
    {
      $this->building = $data->building
            ? response_dto::transform(building_dto::class, $data->building)
            : null;
        return $this;
    }
}

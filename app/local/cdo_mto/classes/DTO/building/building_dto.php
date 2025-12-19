<?php

namespace local_cdo_mto\DTO\building;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class building_dto extends base_dto {

    public $element_type;
    public $element;
    public $element_characteristics;

  protected function get_object_name(): string
    {
        return "building_dto";
    }

    public function build(object $data): self
    {
        $this->element_type = $data->element_type ?? '';

        $this->element = $data->element
            ? response_dto::transform(element_dto::class, $data->element)
            : null;

        $this->element_characteristics = $data->element_characteristics
            ? response_dto::transform(element_characteristics_dto::class, $data->element_characteristics)
            : null;

        return $this;

    }
}

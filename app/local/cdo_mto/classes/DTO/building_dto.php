<?php

namespace local_cdo_mto\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class building_dto extends base_dto {

    public string $element_type;
    public response_dto $element;
    public response_dto $element_characteristics;

    protected function get_object_name(): string
    {
        return "building_dto";
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->element_type = $data->element_type;
        $this->element = $data->element
            ? response_dto::transform(element_dto::class, $data->element)
            : null;
        $this->element_characteristics = $data->element_characteristics
            ? response_dto::transform(element_characteristics_dto::class, $data->element_characteristics)
            : null;
        return $this;
    }
}
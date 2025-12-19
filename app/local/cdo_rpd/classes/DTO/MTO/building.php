<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class building extends base_dto
{
    public string $element_type;
    public response_dto|array $element;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return '\\local_cdo_rpd\\DTO\\mto\\building';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->element_type = $data->element_type;
        $this->element = $data->element
            ? response_dto::transform(
                building_element::class,
                $data->element
            )
            : [];
        return $this;
    }
}
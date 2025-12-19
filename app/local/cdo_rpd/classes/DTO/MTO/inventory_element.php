<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class inventory_element extends base_dto
{
    public response_dto|array $element;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return "inventory_element";
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->element = $data->element
            ? response_dto::transform(
                element::class,
                $data->element
            )
            : [];
        return $this;
    }
}
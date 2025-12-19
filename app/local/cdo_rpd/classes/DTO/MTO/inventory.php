<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class inventory extends base_dto
{
    public array|response_dto $inventory;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
       return '\local_cdo_rpd\DTO\MTO\inventory';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->inventory = $data->inventory
            ? response_dto::transform(
                inventory_element::class,
                $data->inventory
            )
            : [];
        return $this;
    }
}
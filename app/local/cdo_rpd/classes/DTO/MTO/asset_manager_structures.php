<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class asset_manager_structures extends base_dto
{
    public string $error;
    public response_dto|array $data;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return "asset_manager_structures";
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->error = $data->error;
        $this->data = $data->data
            ? response_dto::transform(
                structures_data::class,
                $data->data
            )
            : [];
        return $this;
    }
}
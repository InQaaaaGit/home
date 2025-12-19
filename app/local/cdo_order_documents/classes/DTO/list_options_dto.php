<?php

namespace local_cdo_order_documents\DTO;

use tool_cdo_config\request\DTO\base_dto;

class list_options_dto extends base_dto
{
    public ?string $id;
    public ?string $description;

    protected function get_object_name(): string
    {
        return 'list_options_dto';
    }

    public function build(object $data): base_dto
    {

        $this->id = $data->id ?? '';
        $this->description = $data->description ?? '';

        return $this;
    }
}
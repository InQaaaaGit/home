<?php

namespace local_cdo_rpd\DTO\RPD\management;

use tool_cdo_config\request\DTO\base_dto;

class edu_plan extends base_dto
{

    public string $id;

    protected function get_object_name(): string
    {
        return 'edu_plan';
    }

    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        return $this;
    }
}
<?php

namespace block_cdo_schedule\dto;

use tool_cdo_config\request\DTO\base_dto;

class schedule_period_dto extends base_dto
{
    public string $id;
    public string $name;

    protected function get_object_name(): string
    {
        return '';
    }

    public function build(object $data): base_dto
    {
        $this->id = $data->id ;
        $this->name = $data->name;
        return $this;
    }
}
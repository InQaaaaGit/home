<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;

class get_building_from_1c extends base_dto
{

    public string $code;
    public string $name;
    public string $uid;

    protected function get_object_name(): string
    {
        return '';
    }

    public function build(object $data): base_dto
    {
        $this->code = $data->code;
        $this->name = $data->name;
        $this->uid = $data->uid;
        return $this;
    }
}
<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;

class room extends base_dto
{

    protected function get_object_name(): string
    {
        return '\\local_cdo_rpd\\DTO\\room';
    }

    public function build(object $data): base_dto
    {

        return $this;
    }
}
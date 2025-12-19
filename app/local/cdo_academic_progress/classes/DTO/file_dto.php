<?php

namespace local_cdo_academic_progress\DTO;

use tool_cdo_config\request\DTO\base_dto;

class file_dto extends base_dto
{

    protected function get_object_name(): string
    {
        return "file_dto";
    }

    public function build(object $data): base_dto
    {
        return $this;
    }
}
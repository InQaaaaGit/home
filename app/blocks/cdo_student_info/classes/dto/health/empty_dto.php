<?php

namespace block_cdo_student_info\dto\health;

use tool_cdo_config\request\DTO\base_dto;

class empty_dto extends base_dto
{

    protected function get_object_name(): string
    {
        return 'empty_dto';
    }

    public function build(object $data): base_dto
    {
       return $this;
    }
}
<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class software extends base_dto
{

    public string $fullname;
    public string $uid;

    protected function get_object_name(): string
    {
        return 'software';
    }

    public function build(object $data): base_dto
    {
        $this->fullname = $data->fullname;
        $this->uid = $data->uid;
        return $this;
    }
}
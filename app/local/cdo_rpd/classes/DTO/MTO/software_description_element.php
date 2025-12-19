<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;

class software_description_element extends base_dto
{
    public string $uid;
    public string $fullname;
    public string $expiration;
    protected function get_object_name(): string
    {
       return 'software_description_element';
    }

    public function build(object $data): base_dto
    {
        $this->uid = $data->uid;
        $this->fullname = $data->name;
        $this->expiration = $data->expiration;
        return $this;
    }
}
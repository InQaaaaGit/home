<?php

namespace local_cdo_rpd\DTO\RPD;

use tool_cdo_config\request\DTO\base_dto;

class change_status extends base_dto
{

    public int $status;
    public string $message;
    public bool $status_of_type;

    protected function get_object_name(): string
    {
        return '\\local_cdo_rpd\\DTO\\RPD\\change_status';
    }

    public function build(object $data): base_dto
    {
        $this->status = $data->status;
        $this->message = $data->message;
        $this->status_of_type = $data->status_of_type;
        return $this;
    }
}
<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class developers extends base_dto
{

    public string|null $guid;
    public string|null $user;
    public string|null $module;
    public string|null $user_id;

    protected function get_object_name(): string
    {
        return '';
    }

    public function build(object $data): base_dto
    {
        $this->guid = $data->guid ?? null;
        $this->user = $data->user;
        $this->module = $data->module;
        $this->user_id = $data->user_id;
        return $this;
    }
}
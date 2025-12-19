<?php

namespace local_cdo_rpd\DTO\print_RPD;

use tool_cdo_config\request\DTO\base_dto;

class get_chair_info extends base_dto
{

    public string $Подразделение;
    public string $Кафедра;

    protected function get_object_name(): string
    {
        return '\\local_cdo_rpd\\DTO\\print_RPD\\get_chair_info';
    }

    public function build(object $data): base_dto
    {
        $this->Подразделение = $data->Подразделение;
        $this->Кафедра = $data->Кафедра;
        return $this;
    }
}
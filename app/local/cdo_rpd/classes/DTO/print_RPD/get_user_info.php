<?php

namespace local_cdo_rpd\DTO\print_RPD;

use tool_cdo_config\request\DTO\base_dto;

class get_user_info extends base_dto
{

    public string $Подразделение;
    public string $Должность;
    public string $УченаяСтепень;
    public string $ID;
    public string $УченоеЗвание;

    protected function get_object_name(): string
    {
       return '\\local_cdo_rpd\\DTO\\print_RPD\\get_user_info';
    }

    public function build(object $data): base_dto
    {
        $this->Подразделение = $data->Подразделение;
        $this->Должность = $data->Должность;
        $this->УченаяСтепень = $data->УченаяСтепень;
        $this->ID = $data->ID;
        $this->УченоеЗвание = $data->УченоеЗвание;

        return $this;
    }
}
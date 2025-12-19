<?php

namespace local_cdo_rpd\DTO\print_RPD;

use tool_cdo_config\request\DTO\base_dto;

class get_plan extends base_dto
{
    public string $Факультет;
    public string $ВыпускающаяКафедра;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return '\\local_cdo_rpd\\DTO\\print_RPD\\get_plan';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->Факультет = $data->Факультет;
        $this->ВыпускающаяКафедра = $data->ВыпускающаяКафедра;
        return $this;
    }
}
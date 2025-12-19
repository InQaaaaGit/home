<?php

namespace local_cdo_certification_sheet\DTO;

use tool_cdo_config\request\DTO\base_dto;

class points extends base_dto
{

    public string $minPoint;
    public string $maxPoint;

    protected function get_object_name(): string
    {
        return 'points';
    }

    public function build(object $data): base_dto
    {
        $this->minPoint = $data->minPoint;
        $this->maxPoint = $data->maxPoint;
        return $this;
    }
}
<?php

namespace local_cdo_education_plan\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class period_dto extends base_dto
{
    public string $name;
    public int $number;

    protected function get_object_name(): string
    {
        return "period";
    }

    public function build(object $data): base_dto
    {
        $this->name = $data->name ?? null;
        $this->number = $data->number ?? null;
        return $this;
    }
}
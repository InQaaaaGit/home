<?php

namespace local_cdo_debts\DTO\academic;

use tool_cdo_config\request\DTO\base_dto;

final class study_load_type_dto extends base_dto
{
    public string $id;
    public string $name;
    public string $short_name;

    protected function get_object_name(): string
    {
        return "study_load_type";
    }

    public function build(object $data): base_dto
    {
        $this->id = $data->id ?? null;
        $this->name = $data->name ?? null;
        $this->short_name = $data->short_name ?? null;

        return $this;
    }
}
<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class selected_theme extends base_dto
{

    public string $id;
    public string $name_segment;

    protected function get_object_name(): string
    {
        return 'selected_theme';
    }

    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->name_segment = $data->name_segment;
        return $this;
    }
}
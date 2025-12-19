<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class selected_value extends base_dto
{

    protected function get_object_name(): string
    {
        return 'sv';
    }

    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->competenceguid = $data->competenceguid;
        $this->title = $data->title;

        return $this;
    }
}
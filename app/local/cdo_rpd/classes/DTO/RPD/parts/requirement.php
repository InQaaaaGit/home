<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class requirement extends base_dto
{

    public string $own;
    public string $know;
    public string$beAbleTo;

    protected function get_object_name(): string
    {
        return 'requirement';
    }

    public function build(object $data): base_dto
    {
        $this->own = $data->own;
        $this->know = $data->know;
        $this->beAbleTo = $data->beAbleTo;
        return $this;
    }
}
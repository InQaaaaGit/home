<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class part1 extends base_dto
{

    public string $target;
    public string $taskfordisc;

    protected function get_object_name(): string
    {
        return '';
    }

    public function build(object $data): base_dto
    {
        $this->target = $data->target;
        $this->taskfordisc = $data->taskfordisc;
        return $this;
    }
}
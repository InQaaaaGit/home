<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class load extends base_dto
{

    public string $type;
    public int $value;
    public string $typeguid;

    protected function get_object_name(): string
    {
        return 'load';
    }

    public function build(object $data): base_dto
    {
        $this->type = $data->type;
        $this->value = $data->value;
        $this->typeguid = $data->typeguid;
        return $this;
    }
}
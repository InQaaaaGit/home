<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class enrole_types extends base_dto
{

    public string $name;
    public string $code;
    public bool $required;
    public string $template;

    protected function get_object_name(): string
    {
        return 'enrole_types';
    }

    public function build(object $data): base_dto
    {
        $this->name = $data->name;
        $this->code = $data->code;
        $this->required = $data->required;
        $this->template = $data->template;
        return $this;
    }
}
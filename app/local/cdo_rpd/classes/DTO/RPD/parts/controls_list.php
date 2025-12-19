<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class controls_list extends base_dto
{

    public string $name;
    public string $code;
    public string $template;
    public bool $required;

    protected function get_object_name(): string
    {
        return 'controls_list';
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
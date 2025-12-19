<?php

namespace local_cdo_mto\DTO\room;

use tool_cdo_config\request\DTO\base_dto;

class element_dto extends base_dto {

    public string $uid;
    public string $code;
    public string $name;

    protected function get_object_name(): string
    {
        return "element_dto";
    }

    public function build(object $data): self
    {
        $this->uid  = $data->uid ?? '';
        $this->code = $data->code ?? '';
        $this->name = $data->name ?? '';

        return $this;
    }
}

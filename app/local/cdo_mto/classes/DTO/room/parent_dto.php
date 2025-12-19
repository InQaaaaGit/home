<?php

namespace local_cdo_mto\DTO\room;

use tool_cdo_config\request\DTO\base_dto;

class parent_dto extends base_dto {

    public string $uid;
    public string $code;
    public string $name;
    public string $type;

    protected function get_object_name(): string
    {
        return "parent_dto";
    }

    public function build(object $data): self
    {
        $this->uid  = $data->uid ?? '';
        $this->code = $data->code ?? '';
        $this->name = $data->name ?? '';
        $this->type = $data->type ?? '';

        return $this;
    }
}

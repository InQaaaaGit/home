<?php

namespace local_cdo_debts\DTO\academic;

use tool_cdo_config\request\DTO\base_dto;

class options_dto extends base_dto
{
    public string $text;
    public string $value;

    protected function get_object_name(): string
    {
        return "options_dto";
    }

    public function build(object $data): base_dto
    {
        $this->text = $data->text ?? '';
        $this->value = $data->value ?? '';
        return $this;
    }
}
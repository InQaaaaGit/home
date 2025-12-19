<?php

namespace block_cdo_schedule\dto;

use tool_cdo_config\request\DTO\base_dto;

class set_grade_dto extends base_dto
{
    public bool $success;
    public ?string $error;

    protected function get_object_name(): string
    {
        return 'set';
    }

    public function build(object $data): base_dto
    {
        $this->success = $data->success;
        $this->error = $data->error;
        return $this;
    }
}
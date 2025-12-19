<?php

namespace block_cdo_student_info\dto\diplomas;

use tool_cdo_config\request\DTO\base_dto;

class elements_dto extends base_dto
{

    public string $name;
    public string $control_type;
    public string $period_control;
    public string $mark;

    protected function get_object_name(): string
    {
        return 'elements_dto';
    }

    public function build(object $data): base_dto
    {
        $this->name = $data->name;
        $this->control_type = $data->control_type;
        $this->period_control = $data->period_control;
        $this->mark = $data->mark;
        return $this;
    }
}
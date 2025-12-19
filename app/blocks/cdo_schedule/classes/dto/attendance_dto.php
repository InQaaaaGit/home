<?php

namespace block_cdo_schedule\dto;

use tool_cdo_config\request\DTO\base_dto;

class attendance_dto extends base_dto
{
    public string $student;
    public string $student_fio;
    public string $attendance_status;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'attendance';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->student = $data->student;
        $this->student_fio = $data->student_fio;
        $this->attendance_status = $data->attendance_status;
        return $this;
    }
}
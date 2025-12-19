<?php

namespace local_cdo_academic_progress\DTO\school;

use tool_cdo_config\request\DTO\base_dto;

class school_academic_progress_dto extends base_dto
{

    public string $organization;
    public string $year;
    public int $lesson_number;
    public string $teacher;
    public int $grade;
    public string $reason;
    public string $discipline;

    protected function get_object_name(): string
    {
        return 'school_academic_progress';
    }

    public function build(object $data): base_dto
    {
       # var_dump($data);
        $this->organization = $data->organization;
        $this->year = $data->year;
        $this->teacher = $data->teacher;
        $this->lesson_number = $data->lesson_number;
        $this->grade = $data->grade;
        $this->reason = $data->reason;
        $this->discipline = $data->discipline;
        return $this;
    }
}
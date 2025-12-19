<?php

namespace block_cdo_schedule\dto;

use tool_cdo_config\request\DTO\base_dto;

class attendance_data_dto extends base_dto
{
    public ?string $name;
    public ?string $date1c;
    public ?string $training_course;
    public ?string $lesson_type;
    public ?string $group;
    public ?string $period_of_study;
    public ?string $discipline;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'attendance_data';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->name = $data->name;
        $this->date1c = $data->date1c;
        $this->training_course = $data->training_course;
        $this->lesson_type = $data->lesson_type;
        $this->group = $data->group;
        $this->period_of_study = $data->period_of_study;
        $this->discipline = $data->discipline;
        return $this;
    }
}
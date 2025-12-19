<?php

namespace block_cdo_schedule\dto;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class schedule_lesson_dto extends base_dto
{

    public response_dto $discipline;
    public response_dto $lesson_type;
    public response_dto $period_of_study;
    public string $lesson_key;
    public string $lesson_replacement_key;
    public string $edu_plan;
    public string $training_course;

    /**
     * @param object $data
     * @return $this
     * @throws \coding_exception
     * @throws cdo_type_response_exception
     */
    public function build(object $data): self
    {
        $this->discipline = $data->discipline
            ? response_dto::transform(schedule_discipline_dto::class, $data->discipline)
            : null;
        $this->lesson_type = $data->lesson_type
            ? response_dto::transform(schedule_lesson_type_dto::class, $data->lesson_type)
            : null;
        $this->period_of_study = $data->period_of_study
            ? response_dto::transform(schedule_period_of_study_dto::class, $data->period_of_study)
            : null;
        $this->lesson_key = $data->lesson_key ?? null;
        $this->lesson_replacement_key = $data->lesson_replacement_key ?? "";
        $this->edu_plan = $data->edu_plan ?? "";
        $this->training_course = $data->training_course ?? "";
        return $this;
    }

    /**
     * @return string
     */
    protected function get_object_name(): string
    {
        return "schedule_group";
    }
}
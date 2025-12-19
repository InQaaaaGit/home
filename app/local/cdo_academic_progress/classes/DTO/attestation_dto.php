<?php

namespace local_cdo_academic_progress\DTO;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class attestation_dto extends base_dto
{
    public int $semester;
    public string $date;
    public response_dto $discipline;
    public string $type_control;
    public string $type_control_short;
    public string $mark;
    public string $mark_short;
    public int $score;
    public ?int $color_id;
    public ?bool $retake;
    public array|response_dto $teacher;
    public ?string $course_work;
    public ?bool $course_work_show;
    public ?string $manager_practice;
    public ?string $place_practice;
    public ?string $hours;


    protected function get_object_name(): string
    {
        return "attestation";
    }

	/**
	 * @param object $data
	 * @return base_dto
	 * @throws cdo_config_exception
	 */
    public function build(object $data): base_dto
    {
        $this->semester = $data->semester ?? 0;
        $this->date = $data->date ?? "";
        $this->discipline = $data->discipline
            ? response_dto::transform(directory_dto::class, $data->discipline)
            : null;
        $this->type_control = $data->type_control ?? null;
        $this->type_control_short = $data->type_control_short ?? null;
        $this->mark = $data->mark ?? null;
        $this->mark_short = $data->mark_short ?? null;
        $this->score = $data->score ?? null;
        $this->color_id = $data->color_id ?? null;
        $this->retake = $data->retake ?? false;
        $this->course_work = $data->course_work ?? '';
        $this->course_work_show = $data->course_work_show ?? false;
        $this->manager_practice = $data->manager_practice ?? "";
        $this->place_practice = $data->place_practice ?? "";
        $this->hours = $data->hours ?? "";

        $this->teacher = $data->teacher
            ? response_dto::transform(directory_dto::class, $data->teacher)
            : [];
        return $this;
    }
}
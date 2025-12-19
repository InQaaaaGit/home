<?php

namespace block_cdo_schedule\dto;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class schedule_item_dto extends base_dto {

	public response_dto $groups;
	public response_dto|array $subgroups;
	public response_dto $teachers;
    public string $date;
	public string $order;
	public response_dto $lesson;
    public string $start_time;
    public string $end_time;
    public response_dto $classrooms;

    /**
     * @param object $data
     * @return $this
     * @throws \coding_exception
     * @throws cdo_type_response_exception
     */
	public function build(object $data): self {
        $this->groups = $data->groups
            ? response_dto::transform(schedule_group_dto::class, $data->groups)
            : null;
		$this->subgroups = $data->subgroups
            ? response_dto::transform(schedule_group_dto::class, $data->subgroups)
            : [];
        $this->teachers = $data->teachers
            ? response_dto::transform(schedule_teacher_dto::class, $data->teachers)
            : null;
		$this->date = $data->date ?? null;
		$this->order = $data->order ?? null;
		$this->lesson = $data->lesson
			? response_dto::transform(schedule_lesson_dto::class, $data->lesson)
			: null;
        $this->start_time = $data->start_time ?? null;
        $this->end_time = $data->end_time ?? null;
        $this->classrooms = $data->classrooms
			? response_dto::transform(schedule_classroom_dto::class, $data->classrooms)
			: null;
		return $this;
	}

	/**
	 * @return string
	 */
	protected function get_object_name(): string {
		return "schedule_item";
	}
}

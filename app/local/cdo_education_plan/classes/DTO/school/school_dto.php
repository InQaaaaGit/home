<?php

namespace local_cdo_education_plan\DTO\school;

use tool_cdo_config\request\DTO\base_dto;

final class school_dto extends base_dto
{
    public string $discipline;
    public string $hours;
    public string $section;

    protected function get_object_name(): string
    {
        return "school_dto";
    }

	/**
	 * @param object $data
	 * @return base_dto
	 */
    public function build(object $data): base_dto
    {
        $this->discipline = $data->discipline ?? null;
        $this->hours = $data->hours ?? null;
        $this->section = $data->section ?? null;
        return $this;
    }
}
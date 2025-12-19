<?php

namespace local_cdo_education_plan\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class discipline_dto extends base_dto
{
    public string $id;
    public string $name;
    public string $recordtype;
    public string $guidfile;

    protected function get_object_name(): string
    {
        return "discipline";
    }

	/**
	 * @param object $data
	 * @return base_dto
	 */
    public function build(object $data): base_dto
    {
        $this->id = $data->id ?? null;
        $this->name = $data->name ?? null;
        $this->recordtype = $data->recordtype ?? null;
        $this->guidfile = $data->guidfile ?? null;
        return $this;
    }
}
<?php

namespace local_cdo_education_scoring\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;

final class student_report_dto extends base_dto
{
    public string $id;
    public string $fullname;
    public string $group;
    public string $group_id;
    public string $speciality;

    protected function get_object_name(): string
    {
        return "student_report";
    }

    /**
     * @param object $data
     * @return base_dto
     * @throws coding_exception
     * @throws cdo_type_response_exception
     */
    public function build(object $data): base_dto
    {
        $this->id = (string)($data->id ?? '');
        $this->fullname = (string)($data->fullname ?? '');
        $this->group = (string)($data->group ?? '');
        $this->group_id = (string)($data->group_id ?? '');
        $this->speciality = (string)($data->speciality ?? '');

        return $this;
    }
}


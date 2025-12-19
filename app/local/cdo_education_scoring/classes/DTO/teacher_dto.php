<?php

namespace local_cdo_education_scoring\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;

final class teacher_dto extends base_dto
{
    public int $id;
    public string $fullname;
    public ?string $email;

    protected function get_object_name(): string
    {
        return "teacher";
    }

    /**
     * @param object $data
     * @return base_dto
     * @throws coding_exception
     * @throws cdo_type_response_exception
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id ?? 0;
        $this->fullname = $data->fullname ?? '';
        $this->email = $data->email ?? null;

        return $this;
    }
}


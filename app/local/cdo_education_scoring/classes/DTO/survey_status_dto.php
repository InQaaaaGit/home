<?php

namespace local_cdo_education_scoring\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;

final class survey_status_dto extends base_dto
{
    public bool $status;
    public ?string $message;

    protected function get_object_name(): string
    {
        return "survey_status";
    }

    /**
     * @param object $data
     * @return base_dto
     * @throws coding_exception
     * @throws cdo_type_response_exception
     */
    public function build(object $data): base_dto
    {
        $this->status = $data->status ?? false;
        $this->message = $data->message ?? null;

        return $this;
    }
}


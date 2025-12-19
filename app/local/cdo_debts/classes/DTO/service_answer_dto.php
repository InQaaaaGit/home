<?php

namespace local_cdo_debts\DTO;

use local_cdo_debts\DTO\academic\create_request_retake_dto;
use tool_cdo_config\request\DTO\base_dto;

final class service_answer_dto extends create_request_retake_dto
{
    protected function get_object_name(): string
    {
        return "service_answer_dto";
    }
}
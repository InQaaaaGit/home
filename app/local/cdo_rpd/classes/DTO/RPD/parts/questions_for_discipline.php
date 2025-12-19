<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class questions_for_discipline extends base_dto
{

    public array|response_dto $lab_work;

    protected function get_object_name(): string
    {
        return 'questions_for_discipline';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->lab_work = $data->lab_work ?
            response_dto::transform(
                lab_work::class,
                $data->lab_work
            ) : [];
        return $this;
    }
}
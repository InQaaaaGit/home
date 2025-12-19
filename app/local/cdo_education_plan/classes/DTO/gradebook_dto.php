<?php

namespace local_cdo_education_plan\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class gradebook_dto extends base_dto
{

    public ?response_dto $eduplan;
    public string $gradebook;
    public string $order;

    protected function get_object_name(): string
    {
        return "universal_dto";
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {

        $this->eduplan = $data->eduplan
            ? response_dto::transform(education_plan_dto::class, $data->eduplan)
            : null;
        $this->gradebook = $data->gradebook;
        $this->order = $data->order;

        return $this;
    }
}
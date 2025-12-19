<?php

namespace block_cdo_student_info\dto\checklist;

use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class checklist_dto extends base_dto
{

    public ?response_dto $checklist;

    /**
     * @return string
     */
    protected function get_object_name(): string
    {
        return "checklist_dto";
    }

    /**
     * @throws cdo_type_response_exception
     */
    public function build(object $data): self
    {
        $this->checklist = $data->checklist ?
            response_dto::transform(checklist_item_dto::class, $data->checklist) : [];
        return $this;
    }
}
<?php

namespace block_cdo_student_info\dto\checklist;

use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class department_dto extends base_dto
{
    public string $department_name;
    public string $department_date;
    public string $mark;
    public string $comment;

    /**
     * @return string
     */
    protected function get_object_name(): string
    {
        return "department_dto";
    }

    public function build(object $data): self
    {
        $this->department_name = $data->department_name ?? null;
        $this->department_date = $data->department_date ?? null;
        $this->mark = $data->mark ?? null;
        $this->comment = $data->comment ?? null ;

        return $this;
    }
}
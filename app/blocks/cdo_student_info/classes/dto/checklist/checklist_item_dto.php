<?php

namespace block_cdo_student_info\dto\checklist;

use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class checklist_item_dto extends base_dto
{
    public string $number;
    public string $record_book;
    public response_dto $department;
    public string $checklist_date;

    /**
     * @return string
     */
    protected function get_object_name(): string
    {
        return "checklist_item_dto";
    }

    /**
     * @throws cdo_type_response_exception
     */
    public function build(object $data): self
    {
        $this->checklist_date = $data->checklist_date;
        $this->number = $data->number;
        $this->record_book = $data->record_book;
        $this->department = $data->department ?
            response_dto::transform(department_dto::class, $data->department) : null;
        return $this;
    }
}
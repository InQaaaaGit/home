<?php

namespace block_cdo_student_info\dto\diplomas;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class diplomas_dto extends base_dto
{

    public string $number;
    public string $series;
    public string $issue_date;
    public string $education_type;
    public string $document_type;
    public response_dto $categories;

    protected function get_object_name(): string
    {
        return 'diplomas_dto';
    }

    public function build(object $data): base_dto
    {
        $this->number = $data->number;
        $this->series = $data->series;
        $this->issue_date = $data->issue_date;
        $this->education_type = $data->education_type;
        $this->document_type = $data->document_type;
        $this->categories = $data->categories
            ? response_dto::transform(categories_dto::class, $data->categories)
            : [];
        return $this;
    }
}
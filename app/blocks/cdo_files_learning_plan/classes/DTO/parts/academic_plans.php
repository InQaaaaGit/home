<?php

namespace block_cdo_files_learning_plan\DTO\parts;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class academic_plans extends base_dto
{
    public string $doc_number;
    public string $full_name;
    public array|response_dto $files;

    protected function get_object_name(): string
    {
        return 'academic_plan';
    }

    public function build(object $data): base_dto
    {
        $this->doc_number = $data->doc_number;
        $this->files = $data->files
            ? response_dto::transform(academic_plans_files::class, $data->files)
            : [];
        $this->full_name = $data->full_name;
        return $this;
    }
}
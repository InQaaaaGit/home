<?php

namespace block_cdo_files_learning_plan\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class education_program_file_dto extends base_dto
{
    public string $comment;
    public ?string $edu_plan;
    public string $id;
    public string $name;

    protected function get_object_name(): string
    {
        return "education_program_file";
    }

    public function build(object $data): base_dto
    {
        $this->comment = $data->comment ?? '';
        $this->edu_plan = $data->edu_plan ?? null;
        $this->id = $data->id ?? '';
        $this->name = $data->name ?? '';
        return $this;
    }
}

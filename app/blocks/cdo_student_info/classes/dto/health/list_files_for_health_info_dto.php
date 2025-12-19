<?php

namespace block_cdo_student_info\dto\health;

use tool_cdo_config\request\DTO\base_dto;

class list_files_for_health_info_dto extends base_dto
{
    public string $id;
    public string $description;
    public string $extension;

    protected function get_object_name(): string
    {
        return 'list_files_for_health_info_dto';
    }

    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->description = $data->description ?? '';
        $this->extension = $data->extention;
        return $this;
    }
}
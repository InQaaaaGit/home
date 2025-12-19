<?php

namespace block_cdo_files_learning_plan\DTO\parts;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class discipline_files_files extends base_dto
{
    public string $filename;
    public string $guidfile;
    public response_dto $section;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'discipline_files';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->filename = $data->filename;
        $this->guidfile = $data->guidfile;
        $this->section = $data->section
            ? response_dto::transform(discipline_files_files_section::class, $data->section)
            : [];
        return $this;
    }
}
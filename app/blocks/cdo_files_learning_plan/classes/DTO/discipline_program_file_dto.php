<?php

namespace block_cdo_files_learning_plan\DTO;

use block_cdo_files_learning_plan\DTO\parts\discipline_files_files;
use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class discipline_program_file_dto extends base_dto
{
    public array|response_dto $files;

    protected function get_object_name(): string
    {
        return "discipline_program_file";
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->files = $data->files
            ? response_dto::transform(discipline_files_files::class, $data->files)
            : [];
        return $this;
    }
}

<?php

namespace local_cdo_mto\DTO\discipline;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class data_dto extends base_dto {

    public $education_program;

    protected function get_object_name(): string
    {
        return "data_dto";
    }

    public function build(object $data): self
    {
      $this->education_program = $data->education_program
            ? response_dto::transform(education_program_dto::class, $data->education_program)
            : null;

        return $this;
    }
}

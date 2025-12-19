<?php

namespace local_cdo_mto\DTO\discipline;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class education_program_dto extends base_dto {

    public $uid;
    public $name;
    public $academic_year;
    public $speciality;
    public $specialisation;
    public $discipline;

  protected function get_object_name(): string
    {
        return "education_program_dto";
    }

    public function build(object $data): self
    {
        $this->uid = $data->uid ?? '';
        $this->name = $data->name ?? '';

        $this->academic_year = $data->academic_year
            ? response_dto::transform(element_dto::class, $data->academic_year)
            : null;
        $this->speciality = $data->speciality
            ? response_dto::transform(element_dto::class, $data->speciality)
            : null;
        $this->specialisation = $data->specialisation
            ? response_dto::transform(element_dto::class, $data->specialisation)
            : null;

        $this->discipline = $data->discipline
            ? response_dto::transform(element_dto::class, $data->discipline)
            : [];

        return $this;

    }
}

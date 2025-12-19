<?php

namespace block_cdo_files_learning_plan\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class education_programs_dto extends base_dto
{


    public string $doc_number;
    public string $year;
    public string $education_type;
    public string $education_type_id;
    public string $education_level_id;
    public string $specialty;
    public string $specialty_id;
    public string $profile;
    public string $profile_id;
    public array $files;
    public array $discipline_files;
    public array $web_links;

    protected function get_object_name(): string
    {
        return "education_programs";
    }

    public function build(object $data): base_dto
    {
        $this->doc_number           = $data->doc_number ?? '';
        $this->year                 = $data->year ?? '';
        $this->education_type       = $data->education_type ?? '';
        $this->education_type_id    = $data->education_type_id ?? '';
        $this->education_level_id   = $data->education_level_id ?? '';
        $this->specialty            = $data->specialty ?? '' ;
        $this->specialty_id         = $data->specialty_id ?? '';
        $this->profile              = $data->profile ?? '';
        $this->profile_id           = $data->profile_id ?? '';
        $this->files                = $data->files ?? [];
        $this->discipline_files     = $data->discipline_files ?? [];
        $this->web_links            = $data->web_links ?? [];
        return $this;
    }
}

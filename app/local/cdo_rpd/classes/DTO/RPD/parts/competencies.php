<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class competencies extends base_dto
{

    public string $id;
    public string $competenceguid;
    public string $title;
    public ?response_dto $requirement;
    public string $short_code;

    protected function get_object_name(): string
    {
        return 'competencies';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->competenceguid = $data->competenceguid;
        $this->title = $data->title;
        $this->requirement = $data->requirement ? response_dto::transform(
            requirement::class,
            $data->requirement
        )
            : null;
        $this->short_code = $data->short_code ?? '';
        return $this;
    }
}
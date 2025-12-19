<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class questions extends base_dto
{

    public string $id;
    public string $questionName;
    public array|response_dto $competences;

    protected function get_object_name(): string
    {
        return 'questions';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->questionName = $data->questionName;
        $this->competences = $data->competences ? response_dto::transform(
            competencies::class,
            $data->competences
        ) : [];

        return $this;
    }
}
<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class questions_for_all_themes extends base_dto
{

    public string $code;
    public response_dto|array $questions;
    public response_dto|array $competences;

    protected function get_object_name(): string
    {
        return 'qfat';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->code = $data->code;
        $this->questions = $data->questions ?
            response_dto::transform(
                questions_all_themes::class,
                $data->questions
            ) : [];
        $this->competences = $data->competences ?
            response_dto::transform(
                questions_all_themes::class,
                $data->competences
            ) : [];
        return $this;
    }
}
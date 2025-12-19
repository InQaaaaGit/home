<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class lab_work extends base_dto
{

    public array|response_dto $themes;
    public array|response_dto $questions;

    protected function get_object_name(): string
    {
        return 'lab_work';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->themes = $data->themes ?
            response_dto::transform(
                themes::class,
                $data->themes
            ) : [];
        $this->questions = $data->questions ?
            response_dto::transform(
                questions::class,
                $data->questions
            ) : [];

        return $this;
    }
}
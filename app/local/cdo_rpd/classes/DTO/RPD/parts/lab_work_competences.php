<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class lab_work_competences extends base_dto
{

    public ?response_dto $questions;
    public ?response_dto $tasks;
    public ?response_dto $themes;

    protected function get_object_name(): string
    {
        return 'lwc';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->questions = $data->questions ?
            response_dto::transform(
                questions_all_themes::class,
                $data->questions
            ) : null;
        $this->tasks = $data->tasks ?
            response_dto::transform(
                themes::class,
                $data->tasks
            ) : null;
        $this->themes = $data->themes ?
            response_dto::transform(
                themes::class,
                $data->themes
            ) : null;
        return $this;
    }
}
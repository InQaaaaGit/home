<?php

namespace local_cdo_rpd\DTO\RPD;

use coding_exception;
use local_cdo_rpd\DTO\RPD\parts\books;
use local_cdo_rpd\DTO\RPD\parts\competencies;
use local_cdo_rpd\DTO\RPD\parts\controls;
use local_cdo_rpd\DTO\RPD\parts\controls_list;
use local_cdo_rpd\DTO\RPD\parts\criteria_list;
use local_cdo_rpd\DTO\RPD\parts\developers;
use local_cdo_rpd\DTO\RPD\parts\forms;
use local_cdo_rpd\DTO\RPD\parts\info;
use local_cdo_rpd\DTO\RPD\parts\MTO;
use local_cdo_rpd\DTO\RPD\parts\part1;
use local_cdo_rpd\DTO\RPD\parts\parts;
use local_cdo_rpd\DTO\RPD\parts\questions_for_all_themes;
use local_cdo_rpd\DTO\RPD\parts\questions_for_discipline;
use local_cdo_rpd\DTO\RPD\parts\title;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class rpd extends base_dto
{

    public response_dto|array $competencies;
    public response_dto|array $developers;
    public ?response_dto $part1;
    public ?response_dto $info;
    public response_dto|array $forms;
    public response_dto|array $controls;
    public response_dto|array $parts;
    public response_dto|array $criteriaList;
    public response_dto|array $controlsList;
    public string $auditWork;
    public string $outwork;
    public ?response_dto $title;
    public response_dto|array $questionsForDiscipline;
    public response_dto|array $books;
    public response_dto|array $questionsForAllThemes;
    public response_dto|array $MTO;

    protected function get_object_name(): string
    {
        return 'rpd';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->competencies = $data->competencies ?
            response_dto::transform(
                competencies::class,
                $data->competencies
            ) : [];
        $this->developers = $data->developers ?
            response_dto::transform(
                developers::class,
                $data->developers
            ) : [];
        $this->info = $data->info ?
            response_dto::transform(
                info::class,
                $data->info
            ) : null;
        $this->part1 = $data->part1 ?
            response_dto::transform(
                part1::class,
                $data->part1
            ) : null;
        $this->forms = $data->forms ?
            response_dto::transform(
                forms::class,
                $data->forms
            ) : [];
        $this->controls = $data->controls ?
            response_dto::transform(
                controls::class,
                $data->controls
            ) : [];
        $this->parts = $data->parts ?
            response_dto::transform(
                parts::class,
                $data->parts
            ) : [];
        $this->controlsList = $data->controlsList ?
            response_dto::transform(
                controls_list::class,
                $data->controlsList
            ) : [];
        $this->auditWork = $data->auditWork;
        $this->outwork = $data->outwork;
        $this->criteriaList = $data->criteriaList ?
            response_dto::transform(
                criteria_list::class,
                $data->criteriaList
            ) : [];
        $this->title = $data->title ?
            response_dto::transform(
                title::class,
                $data->title
            ) : null;
        $this->questionsForDiscipline = $data->questionsForDiscipline ?
            response_dto::transform(
                questions_for_discipline::class,
                $data->questionsForDiscipline
            ) : [];
        $this->books = $data->books ?
            response_dto::transform(
                books::class,
                $data->books
            ) : [];
        $this->questionsForAllThemes = $data->questionsForAllThemes ?
            response_dto::transform(
                questions_for_all_themes::class,
                $data->questionsForAllThemes
            ) : [];
        $this->MTO = $data->MTO ?
            response_dto::transform(
                MTO::class,
                $data->MTO
            ) : [];
        return $this;
    }
}
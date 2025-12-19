<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class data extends base_dto
{

    public string $name_segment;
    public string $lection;
    public string $lection_za;
    public string $lection_oza;
    public string $practice;
    public string $practice_za;
    public string $practice_oza;
    public string $lab;
    public string $lab_za;
    public string $lab_oza;
    public string $outwork;
    public string $outwork_za;
    public string $outwork_oza;
    public string $interactive;
    public string $interactive_za;
    public string $interactive_oza;
    public string $practicePrepare;
    public string $practicePrepare_za;
    public string $practicePrepare_oza;
    public string $description;
    public string $seminaryQuestion;
    public string $seminaryQuestion_za;
    public string $seminaryQuestion_oza;
    public string $description_practice;
    public string $description_practice_za;
    public string $description_practice_oza;
    public string $description_outwork;
    public string $description_outwork_za;
    public string $description_outwork_oza;
    public string $id;
    public string $type;
    public string $all;
    public response_dto|array $data;

    protected function get_object_name(): string
    {
        return 'data';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->name_segment = $data->name_segment;
        $this->lection = $data->lection;
        $this->lection_za = $data->lection_za;
        $this->lection_oza = $data->lection_oza;
        $this->practice = $data->practice;
        $this->practice_za = $data->practice_za;
        $this->practice_oza = $data->practice_oza;
        $this->lab = $data->lab;
        $this->lab_za = $data->lab_za;
        $this->lab_oza = $data->lab_oza;
        $this->outwork = $data->outwork;
        $this->outwork_za = $data->outwork_za;
        $this->outwork_oza = $data->outwork_oza;
        $this->interactive = $data->interactive;
        $this->interactive_za = $data->interactive_za;
        $this->interactive_oza = $data->interactive_oza;
        $this->practicePrepare = $data->practicePrepare;
        $this->practicePrepare_za = $data->practicePrepare_za;
        $this->practicePrepare_oza = $data->practicePrepare_oza;
        $this->description = $data->description;
        $this->seminaryQuestion = $data->seminaryQuestion;
        $this->seminaryQuestion_za = $data->seminaryQuestion_za;
        $this->seminaryQuestion_oza = $data->seminaryQuestion_oza;
        $this->description_practice = $data->description_practice;
        $this->description_practice_za = $data->description_practice_za;
        $this->description_practice_oza = $data->description_practice_oza;
        $this->description_outwork = $data->description_outwork;
        $this->description_outwork_za = $data->description_outwork_za;
        $this->description_outwork_oza = $data->description_outwork_oza;
        $this->id = $data->id;
        $this->type = $data->type;
        $this->all = $data->all;
        $this->data = $data->data ?
            response_dto::transform(
                data_data::class,
                $data->data
            ) : [];
        return $this;
    }
}
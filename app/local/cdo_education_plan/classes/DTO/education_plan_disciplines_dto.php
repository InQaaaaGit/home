<?php

namespace local_cdo_education_plan\DTO;


use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class education_plan_disciplines_dto extends base_dto
{
    public response_dto $discipline;
    public ?response_dto $formcontrol;
    public int $summary;
    public int $labaratory;
    public int $lection;
    public int $practice;
    public int $selflearning;
    public int $all;

    protected function get_object_name(): string
    {
        return "disciplines";
    }

    /**
     * @param object $data
     * @return $this
     * @throws cdo_config_exception
     */
    public function build(object $data): base_dto
    {
        $this->discipline = $data->discipline
            ? response_dto::transform(discipline_dto::class, $data->discipline)
            : null;
        $this->formcontrol = $data->formcontrol
            ? response_dto::transform(directory_dto::class, $data->formcontrol)
            : null;
        $this->summary = $data->summary ?? null;
        $this->labaratory = $data->labaratory ?? null;
        $this->lection = $data->lection ?? null;
        $this->practice = $data->practice ?? null;
        $this->selflearning = $data->selflearning ?? null;
        $this->all = $data->all ?? null;
        return $this;
    }
}
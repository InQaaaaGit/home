<?php

namespace block_cdo_schedule\dto;

use coding_exception;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class schedule_period_of_study_dto extends base_dto
{

    public string $id;
    public string $name;
    public string $semestr_number;
    public string $start_year;
    public string $end_year;
    public response_dto|null $period;

    /**
     * @param object $data
     * @return $this
     * @throws coding_exception
     * @throws cdo_type_response_exception
     */
    public function build(object $data): self
    {
        $this->id = $data->id ?? null;
        $this->name = $data->name ?? null;
        $this->semestr_number = $data->semestr_number ?? null;
        $this->start_year = $data->start_year ?? null;
        $this->end_year = $data->end_year ?? null;
        $this->period = $data->period
            ? response_dto::transform(schedule_period_dto::class, $data->period)
            : null;
        return $this;
    }

    /**
     * @return string
     */
    protected function get_object_name(): string
    {
        return "schedule_period_of_study";
    }
}
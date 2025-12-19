<?php

namespace local_cdo_education_plan\DTO;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class curriculum_entry_dto extends base_dto
{
    public response_dto $period;
    public response_dto $disciplines;
    public ?response_dto $totals;

    protected function get_object_name(): string
    {
        return "curriculum_entry";
    }

    /**
     * @param object $data
     * @return $this
     * @throws cdo_config_exception
     */
    public function build(object $data): base_dto
    {
        $this->period = $data->period
            ? response_dto::transform(period_dto::class, $data->period)
            : null;
        $this->disciplines = $data->disciplines
            ? response_dto::transform(education_plan_disciplines_dto::class, $data->disciplines)
            : null;
        $this->totals = $data->totals
            ? response_dto::transform(education_plan_totals_dto::class, $data->totals)
            : null;

        return $this;
    }
}
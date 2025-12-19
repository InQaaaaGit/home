<?php

namespace local_cdo_education_plan\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class education_plan_totals_dto extends base_dto
{

    public string $discipline;
    public string $formcontrol;
    public int $summary;
    public int $labaratory;
    public int $lection;
    public int $practice;
    public int $selflearning;
    public int $all;

    protected function get_object_name(): string
    {
        return "totals";
    }

	/**
	 * @param object $data
	 * @return base_dto
	 */

    public function build(object $data): base_dto
    {
        $this->discipline = $data->discipline ?? null;
        $this->formcontrol = $data->formcontrol ?? null;
        $this->summary = $data->summary ?? null;
        $this->labaratory = $data->labaratory ?? null;
        $this->lection = $data->lection ?? null;
        $this->practice = $data->practice ?? null;
        $this->selflearning = $data->selflearning ?? null;
        $this->all = $data->all ?? null;
        return $this;
    }
}
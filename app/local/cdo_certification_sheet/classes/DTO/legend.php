<?php

namespace local_cdo_certification_sheet\DTO;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class legend extends base_dto
{
    public response_dto|array $points;
    public int $color;
    public string $grade;
    public string $name;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->name = $data->name;
        $this->grade = $data->grade;
        $this->color = $data->color;
        $this->points = $data->points
            ? response_dto::transform(points::class, $data->points)
            : [];
        return $this;
    }
}
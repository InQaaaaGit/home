<?php

namespace local_cdo_certification_sheet\DTO;

use tool_cdo_config\request\DTO\base_dto;

class grade extends base_dto
{
    public string $guid;
    public string $name;
    public int $color;
    public int $point;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'grade';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->guid = $data->guid;
        $this->name = $data->name;
        $this->color = $data->color;
        $this->point = $data->point;
        return $this;
    }
}
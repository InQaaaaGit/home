<?php

namespace block_cdo_schedule\dto;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class schedule_group_dto extends base_dto
{

    public string $id;
    public string $name;
    public string $subgroup;
    public string $subgroup_id;

    /**
     * @param object $data
     * @return $this
     * @throws cdo_config_exception
     */
    public function build(object $data): self
    {
        $this->id = $data->id ?? null;
        $this->name = $data->name ?? null;
        $this->subgroup = $data->subgroup ?? null;
        $this->subgroup_id = $data->subgroup_id ?? null;
        return $this;
    }

    /**
     * @return string
     */
    protected function get_object_name(): string
    {
        return "schedule_group";
    }
}
<?php

namespace block_cdo_schedule\dto;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class schedule_lesson_type_dto extends base_dto
{

    public string $id;
    public string $uid;
    public string $name;

    /**
     * @param object $data
     * @return $this
     */
    public function build(object $data): self
    {
        $this->id = $data->id ?? null;
        $this->uid = $data->uid ?? null;
        $this->name = $data->name ?? null;
        return $this;
    }

    /**
     * @return string
     */
    protected function get_object_name(): string
    {
        return "schedule_lesson_type";
    }
}
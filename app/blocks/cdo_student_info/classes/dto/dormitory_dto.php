<?php

namespace block_cdo_student_info\dto;

use tool_cdo_config\request\DTO\base_dto;

final class dormitory_dto extends base_dto {

    public ?string $room;
    public ?string $name;

    /**
     * @return string
     */
    protected function get_object_name(): string {
        return "dormitory_dto";
    }

    public function build(object $data): base_dto
    {
        $this->room = $data->room ?? null;
        $this->name = $data->name ?? null;
        return $this;
    }
}
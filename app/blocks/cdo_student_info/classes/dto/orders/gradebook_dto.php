<?php

namespace block_cdo_student_info\dto\orders;

use tool_cdo_config\request\DTO\base_dto;

final class gradebook_dto extends base_dto
{

    public string $number;
    public string $order_type;
    public string $date;

    protected function get_object_name(): string
    {
        return "get_gradebook_dto";
    }

    public function build(object $data): base_dto
    {
        $this->number = $data->number;
        $this->date = $data->date;
        $this->order_type = $data->order_type;
        return $this;
    }
}
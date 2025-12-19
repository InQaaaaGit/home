<?php

namespace block_cdo_student_info\dto\orders;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class orders_dto extends base_dto
{
    public response_dto $gradebooks;

    protected function get_object_name(): string
    {
        return "my_orders";
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
      /*
        $this->number = $data->number;
        $this->data = $data->data;
        $this->order_type = $data->order_type;*/
        $this->gradebooks = $data->gradebooks
            ? response_dto::transform(gradebooks_dto::class, $data->gradebooks)
            : null;
        return $this;

    }
}
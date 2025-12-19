<?php

namespace block_cdo_student_info\dto\orders;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class gradebooks_dto extends base_dto
{

    public string $name;
    public response_dto $data;
    public string $id;

    protected function get_object_name(): string
    {
        return "get_gradebooks_dto";
    }

    public function build(object $data): base_dto
    {
        #var_dump($data);
        $this->name = $data->name ?? '';
        $this->id = $data->id;
        $this->data = $data->data
            ? response_dto::transform(gradebook_dto::class, $data->data)
            : [];
        return $this;
    }
}
<?php

namespace block_cdo_student_info\dto\diplomas;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class categories_dto extends base_dto
{

    public string $name;
    public response_dto $elements;

    protected function get_object_name(): string
    {
        return 'categories_dto';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->name = $data->name;
        $this->elements = $data->elements
            ? response_dto::transform(elements_dto::class, $data->elements)
            : [];
        return $this;
    }
}
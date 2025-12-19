<?php

namespace local_cdo_rpd\DTO\RPD;

use local_cdo_rpd\DTO\literature_data;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class search_literature extends base_dto
{

    public string $error;
    public int $total;
    public response_dto|array $data;

    protected function get_object_name(): string
    {
        return '\\local_cdo_rpd\\DTO\\RPD\\search_literature';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws \coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->error = $data->error;
        $this->total = $data->total;
        $this->data = $data->data
            ? response_dto::transform(
                literature_data::class,
                $data->data
            )
            : [];
        return $this;
    }
}
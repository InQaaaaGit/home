<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class parts extends base_dto
{

    public string $name_segment;
    public string $id;
    public string $type;
    public string $all;
    public string $all_za;
    public string $all_oza;
    public response_dto $data;

    protected function get_object_name(): string
    {
        return 'parts';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->name_segment = $data->name_segment;
        $this->id = $data->id;
        $this->type = $data->type;
        $this->all = $data->all;
        $this->all_za = $data->all_za;
        $this->all_oza = $data->all_oza;
        $this->data = $data->data ?
            response_dto::transform(
                data::class,
                $data->data
            ) : [];

        return $this;
    }
}
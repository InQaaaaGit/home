<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class MTO extends base_dto
{

    public string $uid;
    public string $code;
    public string $name;
    public response_dto|array $auditorium;

    protected function get_object_name(): string
    {
        return 'MTO';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->uid = $data->uid;
        $this->code = $data->code ?? '';
        $this->name = $data->name;
        $this->auditorium = $data->auditorium ?
            response_dto::transform(
                auditorium::class,
                $data->auditorium
            ) : [];
        return $this;
    }
}
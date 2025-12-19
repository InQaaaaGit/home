<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class auditorium extends base_dto
{

    public string $uid;
    public ?response_dto $code;
    public string $name;
    public ?response_dto $software;
    public array $inventory;

    protected function get_object_name(): string
    {
        return 'auditorium';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->uid = $data->uid;
        $this->code = $data->code ?
            response_dto::transform(
                code::class,
                $data->code
            ) : null;
        $this->name = $data->name;
        $this->inventory = $data->inventory;
        $this->software = $data->software ?
            response_dto::transform(
                software::class,
                $data->software
            ) : null;

        return $this;
    }
}
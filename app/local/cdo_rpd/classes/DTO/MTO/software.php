<?php

namespace local_cdo_rpd\DTO\MTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class software extends base_dto
{

    public response_dto|array $software;

    protected function get_object_name(): string
    {
        return 'software';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->software = $data->software
            ? response_dto::transform(
                software_description::class,
                $data->software
            )
            : [];
        return $this;
    }
}
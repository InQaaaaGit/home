<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class controls extends base_dto
{

    public string $enrole;
    public string $code;
    public ?response_dto $enroleTypes;

    protected function get_object_name(): string
    {
        return 'controls';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->enrole = $data->enrole;
        $this->code = $data->code;
        $this->enroleTypes = $data->enroleTypes ?
            response_dto::transform(
                enrole_types::class,
                $data->enroleTypes
            ) : null;
        return $this;
    }
}
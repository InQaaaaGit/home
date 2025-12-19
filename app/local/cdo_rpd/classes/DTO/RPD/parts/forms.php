<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class forms extends base_dto
{

    public response_dto|array $load;
    public string $guidform;
    public string $name;

    protected function get_object_name(): string
    {
        return 'forms';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->name = $data->name;
        $this->guidform = $data->guidform;
        $this->load = $data->load ? response_dto::transform(
            load::class,
            $data->load
        ) : [];

        return $this;
    }
}
<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class get_software_by_auditorium_from_1c extends base_dto
{

    public string $error;
    public array|response_dto $data;

    protected function get_object_name(): string
    {
        return 'gsbaf1c';
    }

    public function build(object $data): base_dto
    {
        $this->error = $data->error;
        $this->data = $data->data
            ? response_dto::transform(
                software::class,
                $data->data
            )
            : [];
        return $this;
    }
}
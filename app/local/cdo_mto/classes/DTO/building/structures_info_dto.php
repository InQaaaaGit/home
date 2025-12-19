<?php

namespace local_cdo_mto\DTO\building;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class structures_info_dto extends base_dto {

    public response_dto $data;
    public string $error;

    protected function get_object_name(): string
    {
        return "structures_info_dto";
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): self
    {
        $this->data = $data->data
            ? response_dto::transform(data_dto::class, $data->data)
            : null;
        $this->error = $data->error;
        return $this;
    }
}

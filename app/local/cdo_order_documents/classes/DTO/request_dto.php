<?php

namespace local_cdo_order_documents\DTO;

use tool_cdo_config\request\DTO\base_dto;

class request_dto extends base_dto
{
    public bool $success;
    public string $message;
    protected function get_object_name(): string
    {
        return 'request';
    }

    public function build(object $data): base_dto
    {
        $this->success = $data->success;
        $this->message = $data->message;

        return $this;
    }
}

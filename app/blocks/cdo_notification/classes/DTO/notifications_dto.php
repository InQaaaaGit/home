<?php

namespace block_cdo_notification\DTO;

use tool_cdo_config\request\DTO\base_dto;

class notifications_dto extends base_dto
{
    public string $header;
    public string $body_message;
    public string $date;
    public string $id;

    protected function get_object_name(): string
    {
        return 'notifications';
    }

    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->date = $data->date;
        $this->body_message = $data->body_message;
        $this->header = $data->header;
        return $this;
    }
}
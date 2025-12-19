<?php

namespace block_cdo_schedule\dto;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class blank_dto extends base_dto
{
    public bool $error;
    public string $message;
    public array|response_dto $attendance;
    public string $guid_attendance;
    public response_dto $attendance_data;


    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->error = $data->error;
        $this->attendance_data = response_dto::transform(attendance_data_dto::class, $data->attendance_data);
        $this->message = $data->message;
        $this->guid_attendance = $data->guid_attendance;
        $this->attendance = $data->attendance
            ? response_dto::transform(attendance_dto::class, $data->attendance)
            : [];
        return $this;
    }
}
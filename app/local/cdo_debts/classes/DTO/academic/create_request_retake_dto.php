<?php

namespace local_cdo_debts\DTO\academic;

use coding_exception;
use local_cdo_debts\DTO\directory_dto;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class create_request_retake_dto extends base_dto
{

    public bool $success;
    public ?string $error_message;
    public ?response_dto $new_status;
    public ?string $date;

    protected function get_object_name(): string
    {
        return "create_request_retake_dto";
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->success = $data->success;
        $this->date = $data->date;
        $this->new_status = $data->new_status
            ? response_dto::transform(directory_dto::class, $data->new_status)
            : null;
        $this->error_message = $data->error_message ?? '';
        return $this;
    }
}
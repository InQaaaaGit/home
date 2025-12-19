<?php

namespace block_cdo_survey\DTO;

use block_cdo_schedule\dto\attendance_dto;
use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class survey_dto extends base_dto
{
    public string $email;
    public string $lastname;
    public string $firstname;
    public response_dto $form_data;

    protected function get_object_name(): string
    {
        return '\\block_cdo_survey\\DTO\\survey_dto';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->email = $data->email ?? '';
        $this->lastname = $data->lastname ?? '';
        $this->firstname = $data->firstname ?? '';
        $this->form_data = $data->form_data
            ? response_dto::transform(survey_form_dto::class, $data->form_data)
            : null;
        return $this;
    }
}
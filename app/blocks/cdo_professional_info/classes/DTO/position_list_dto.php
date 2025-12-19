<?php

namespace block_cdo_professional_info\DTO;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class position_list_dto extends base_dto
{
    public ?string $employment_contract_end_date;
    public ?string $type_of_employment;
    public ?string $current_position;
    public response_dto $department;
    public response_dto $faculty;

    protected function get_object_name(): string
    {
        return "position_list";
    }

    /**
     * @param object $data
     * @return $this
     * @throws cdo_config_exception
     */
    public function build(object $data): base_dto
    {
        $this->employment_contract_end_date = $data->employment_contract_end_date ?? null;
        $this->type_of_employment = $data->type_of_employment ?? null;
        $this->current_position = $data->current_position ?? null;
        $this->faculty = $data->faculty
            ? response_dto::transform(directory_dto::class, $data->faculty)
            : null;
        $this->department = $data->department
            ? response_dto::transform(directory_dto::class, $data->department)
            : null;

        return $this;
    }
}
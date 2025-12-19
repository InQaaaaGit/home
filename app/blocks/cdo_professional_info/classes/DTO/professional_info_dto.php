<?php

namespace block_cdo_professional_info\DTO;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class professional_info_dto extends base_dto
{
    public string $id;
    public string $presentation;
    public ?string $birth_date;
    public string $surname;
    public string $name;
    public ?string $employment_contract_end_date;
    public string $type_of_employment;
    public string $current_position;
    public ?string $academic_degree;
    public ?string $academic_title;
    public response_dto $faculty;
    public response_dto $department;
    public ?response_dto $positions_list;

    /**
     * @param object $data
     * @return $this
     * @throws cdo_config_exception
     */
    public function build(object $data): self
    {
        $this->id = $data->id ?? null;
        $this->presentation = $data->presentation ?? null;
        $this->birth_date = $data->birth_date ?? null;
        $this->surname = $data->surname ?? null;
        $this->academic_degree = $data->academic_degree ?? null;
        $this->academic_title = $data->academic_title ?? null;
        $this->employment_contract_end_date = $data->employment_contract_end_date ?? null;
        $this->type_of_employment = $data->type_of_employment ?? null;
        $this->current_position = $data->current_position ?? null;
        $this->name = $data->name ?? null;
        $this->faculty = $data->faculty
            ? response_dto::transform(directory_dto::class, $data->faculty)
            : null;
        $this->positions_list = $data->positions_list
            ? response_dto::transform(position_list_dto::class, $data->positions_list)
            : null;
        $this->department = $data->department
            ? response_dto::transform(directory_dto::class, $data->department)
            : null;

        return $this;
    }

    protected function get_object_name(): string
    {
        return "professional_info";
    }
}

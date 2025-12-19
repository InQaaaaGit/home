<?php

namespace local_cdo_rpd\DTO\RPD\management;

use local_cdo_rpd\DTO\RPD\parts\info_developers;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class get_rpd_on_department_1c extends base_dto
{
    public string $id;
    public string $direction;
    public string $discipline;
    public string $discipline_code;
    public string $profile;
    public string $year;
    public string $educationLevel;
    public string $trainingLevel;
    public string $module_id;
    public string $module_name;
    public string $status;
    public string $discipline_index;
    public string $type;
    public ?response_dto $developers;
    public string|response_dto $edu_plan;
    public bool $isExecutiveSecretary;
    public bool $main_department;
    public bool $hideCodev;
    public bool $isHODAgreed;
    public bool $isOPOPAgreed;
    public string $typeAndModule;
    public response_dto|array $info;
    public string $librarian_status;
    public string $librarianStatus;

    protected function get_object_name(): string
    {
        return 'get_rpd_on_department_1c';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws \coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->direction = $data->direction;
        $this->discipline = $data->discipline;
        $this->discipline_code = $data->discipline_code;
        $this->profile = $data->profile;
        $this->year = $data->year;
        $this->educationLevel = $data->educationLevel;
        $this->trainingLevel = $data->trainingLevel;
        $this->module_id = $data->module_id;
        $this->module_name = $data->module_name;
        $this->main_department = $data->main_department ?? true;
        $this->hideCodev = $data->hideCodev ?? false;
        $this->isHODAgreed = $data->isHODAgreed;
        $this->isOPOPAgreed = $data->isOPOPAgreed;
        $this->typeAndModule = $data->typeAndModule ?? '';
        $this->discipline_index = $data->discipline_index;
        $this->type = $data->type;
        $this->status = $data->status;
        $this->librarian_status = $data->librarian_status ?? '';
        $this->librarianStatus = $data->librarianStatus ?? '';
        $this->isExecutiveSecretary = property_exists($data, 'isExecutiveSecretary') ? $data->isExecutiveSecretary : false;
        $this->edu_plan = $data->edu_plan ?
            response_dto::transform(
                edu_plan::class,
                $data->edu_plan
            ) : '';
        $this->developers = $data->developers ?
            response_dto::transform(
                info_developers::class,
                $data->developers
            ) : null;
        $this->info = $data->info ?
            response_dto::transform(
                info::class,
                $data->info
            ) : [];

        return $this;
    }
}
<?php

namespace local_cdo_rpd\DTO\RPD\management;

use coding_exception;
use local_cdo_rpd\DTO\RPD\parts\info_developers;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class get_rpd_by_user_id extends base_dto
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
    public bool $isHODAgreed;
    public bool $isOPOPAgreed;
    public string $typeAndModule;
    public response_dto|array $info;
    public bool $is_archive;
    public string $title;
    public string $librarianStatus;
    public string $librarian_status;

    protected function get_object_name(): string
    {
        return 'get_rpd_on_department_1c';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->direction = $data->direction;
        $this->profile = $data->profile;
        $this->discipline = $data->discipline;
        $this->discipline_code = $data->discipline_code;
        $this->year = $data->year;
        $this->status = $data->status;
        $this->educationLevel = $data->educationLevel;
        $this->trainingLevel = $data->trainingLevel;
        $this->module_id = $data->module_id;
        $this->module_name = $data->module_name;
        $this->discipline_index = $data->discipline_index;
        $this->type = $data->type;
        $this->edu_plan = $data->edu_plan ?
            response_dto::transform(
                edu_plan::class,
                $data->edu_plan
            ) : '';
        $this->info = $data->info ?
            response_dto::transform(
                info::class,
                $data->info
            ) : [];
        $this->is_archive = $data->is_archive;
        $this->title = $data->title;
        $this->isHODAgreed = $data->isHODAgreed;
        $this->isOPOPAgreed = $data->isOPOPAgreed;
        $this->developers = $data->developers ?
            response_dto::transform(
                info_developers::class,
                $data->developers
            ) : null;
        $this->librarianStatus = $data->librarianStatus;
        $this->librarian_status = $data->librarian_status;
        return $this;
    }
}
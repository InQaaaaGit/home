<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class info extends base_dto
{

    public ?response_dto $developers;
    public string $discipline;
    public string $id;
    public string $direction;
    public string $profile;
    public string $discipline_code;
    public string $year;
    public string $status;
    public string $educationLevel;
    public string $trainingLevel;
    public string $module_id;
    public string $module_name;
    public string $discipline_index;
    public string $type;
    public string $librarianStatus;

    protected function get_object_name(): string
    {
        return '';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->developers = $data->developers ?
            response_dto::transform(
            info_developers::class,
            $data->developers
        ) : null;
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
        $this->status = $data->status;
        $this->discipline_index = $data->discipline_index;
        $this->type = $data->type;
        $this->librarianStatus = $data->librarianStatus;
        return $this;
    }
}
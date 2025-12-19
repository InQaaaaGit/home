<?php

namespace block_cdo_files_learning_plan\DTO\parts;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class discipline_files extends base_dto
{
    public string $agreed_date;
    public string $agreed_number;
    public string $agreed_structures;
    public string $block;
    public string $comment;
    public ?string $developer;
    public ?string $developer_id;
    public string $discipline_id;
    public string $discipline_index;
    public string $discipline_name;
    public string $discipline_number;
    public string $edu_plan_number;
    public array|response_dto $files;
    public string $module_id;
    public string $module_name;
    public string $notes;
    public string $rpd_id;
    public string $status_rpd;
    public string $type;
    public string $type_id;
    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'discipline_files';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->agreed_date = $data->agreed_date;
        $this->agreed_number = $data->agreed_number;
        $this->agreed_structures = $data->agreed_structures;
        $this->block = $data->block;
        $this->comment = $data->comment;
        $this->developer = $data->developer ?? null;
        $this->developer_id = $data->developer_id ?? null;
        $this->discipline_id = $data->discipline_id;
        $this->discipline_index = $data->discipline_index;
        $this->discipline_name = $data->discipline_name;
        $this->discipline_number = $data->discipline_number;
        $this->edu_plan_number = $data->edu_plan_number;
        $this->files = $data->files
            ? response_dto::transform(discipline_files_files::class, $data->files)
            : [];
        $this->module_id = $data->module_id;
        $this->module_name = $data->module_name;
        $this->notes = $data->notes;
        $this->rpd_id = $data->rpd_id;
        $this->status_rpd = $data->status_rpd;
        $this->type = $data->type;
        $this->type_id = $data->type_id;
        return $this;
    }
}
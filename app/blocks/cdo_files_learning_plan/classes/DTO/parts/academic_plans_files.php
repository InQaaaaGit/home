<?php

namespace block_cdo_files_learning_plan\DTO\parts;

use tool_cdo_config\request\DTO\base_dto;

class academic_plans_files extends base_dto
{
    public string $comment;
    public string $id;
    public string $name;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'academic_plans_files';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->comment = $data->comment;
        $this->id = $data->id;
        $this->name = $data->name;

        return $this;
    }
}
<?php

namespace block_cdo_files_learning_plan\DTO\parts;

use tool_cdo_config\request\DTO\base_dto;

class files extends base_dto
{
    public string $comment;
    public string $description;
    public ?string $edu_plan;
    public string $id;
    public string $name;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'files';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->comment = $data->comment;
        $this->description = $data->description;
        $this->edu_plan = $data->edu_plan ?? null;
        $this->id = $data->id;
        $this->name = $data->name;
        return $this;
    }
}
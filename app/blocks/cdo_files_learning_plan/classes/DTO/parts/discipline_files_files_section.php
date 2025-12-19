<?php

namespace block_cdo_files_learning_plan\DTO\parts;

use tool_cdo_config\request\DTO\base_dto;

class discipline_files_files_section extends base_dto
{
    public int $id;
    public string $name;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'discipline_files_files';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->name = $data->name;
        return $this;
    }
}
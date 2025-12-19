<?php

namespace block_cdo_files_learning_plan\DTO\parts;

use tool_cdo_config\request\DTO\base_dto;

class structures extends base_dto
{
    public string $text;
    public string $value;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'structure';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->text = $data->text;
        $this->value = $data->value;
        return $this;
    }
}
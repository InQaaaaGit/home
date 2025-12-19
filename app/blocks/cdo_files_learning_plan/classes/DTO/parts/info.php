<?php

namespace block_cdo_files_learning_plan\DTO\parts;

use tool_cdo_config\request\DTO\base_dto;

class info extends base_dto
{
    public string $agreed_date;
    public string $agreed_number;
    public string $agreed_structures;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'info';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->agreed_date = $data->agreed_date;
        $this->agreed_number = $data->agreed_number;
        $this->agreed_structures = $data->agreed_structures;

        return $this;
    }
}
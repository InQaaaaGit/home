<?php

namespace local_cdo_rpd\DTO\literature;

use tool_cdo_config\request\DTO\base_dto;

class add_worker_on_special extends base_dto
{
    public bool $result;
    public ?string $error;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'add_worker_on_special';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->result = $data->result;
        $this->error = $data->error ?? '';
        return $this;
    }
}
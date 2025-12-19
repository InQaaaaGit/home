<?php

namespace local_cdo_rpd\DTO\RPD;

use tool_cdo_config\request\DTO\base_dto;

class set_developers extends base_dto
{
    public bool $success;
    public string $info;
    public ?string $guid;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'set';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        //{"success":true,"info":"","guid":""}
        $this->success = $data->success;
        $this->info = $data->info;
        $this->guid = $data->guid ?? null;
        return $this;
    }
}
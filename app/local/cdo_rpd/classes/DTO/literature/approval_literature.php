<?php

namespace local_cdo_rpd\DTO\literature;

use tool_cdo_config\request\DTO\base_dto;

class approval_literature extends base_dto
{
    public ?string $status;
    public ?string $error;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'approval_literature';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->status = $data->status ?? '';
        $this->error = $data->error ?? '';
        return $this;
    }
}
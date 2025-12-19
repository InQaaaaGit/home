<?php

namespace local_cdo_rpd\DTO\literature;

use tool_cdo_config\request\DTO\base_dto;

class send_literature_for_approve extends base_dto
{

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'literature';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        return $this;
    }
}
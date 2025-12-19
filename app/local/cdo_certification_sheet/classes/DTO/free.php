<?php

namespace local_cdo_certification_sheet\DTO;

use tool_cdo_config\request\DTO\base_dto;

class free extends base_dto
{

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return '\\local_cdo_order_documents\\local_cdo_order_documents\\free';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        return $this;
    }
}
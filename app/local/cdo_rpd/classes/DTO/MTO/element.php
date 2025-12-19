<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;

class element extends base_dto
{
    public string $uid;
    public string $fullname;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return '\\local_cdo_rpd\\DTO\\mto\\element';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->uid = $data->uid;
        $this->fullname = $data->full_name;
        return $this;
    }
}
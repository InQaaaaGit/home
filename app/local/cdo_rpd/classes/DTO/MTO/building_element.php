<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;

class building_element extends base_dto
{
    public string $uid;
    public string $name;
    public string $code;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'building_element';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->uid = $data->uid;
        $this->name = $data->name;
        $this->code = $data->code;
        return $this;
    }
}
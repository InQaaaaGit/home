<?php

namespace local_cdo_rpd\DTO\RPD\management;

use tool_cdo_config\request\DTO\base_dto;

class info extends \tool_cdo_config\request\DTO\base_dto
{
    public string $date;
    public string $event;

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
    public function build(object $data): \tool_cdo_config\request\DTO\base_dto
    {
        $this->date = $data->date;
        $this->event = $data->event;

        return $this;
    }
}
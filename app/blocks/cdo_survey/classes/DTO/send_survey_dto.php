<?php

namespace block_cdo_survey\DTO;

use tool_cdo_config\request\DTO\base_dto;

class send_survey_dto extends base_dto
{

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {

        return $this;
    }
}
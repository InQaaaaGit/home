<?php

namespace local_cdo_rpd\DTO\RPD\parts\fos;

use tool_cdo_config\request\DTO\base_dto;

class first_template extends base_dto
{

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
       return 'first_template';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        //$this->
        return $this;
    }
}
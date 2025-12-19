<?php

namespace local_cdo_rpd\DTO\literature;

use tool_cdo_config\request\DTO\base_dto;

class librarian extends base_dto
{
    public string $id;
    public string $user;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'librarian';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->user = $data->user;
        return $this;
    }
}
<?php

namespace local_cdo_rpd\DTO\literature;

use tool_cdo_config\request\DTO\base_dto;

class get_list_library_workers extends base_dto
{
    public string $id;
    public string $user;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'library_worker';
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
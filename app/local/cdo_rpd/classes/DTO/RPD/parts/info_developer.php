<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class info_developer extends base_dto
{

    public string $blockControl;
    public string $id;
    public string $user;
    public string $guid;

    protected function get_object_name(): string
    {
        return 'info_developer';
    }

    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->user = $data->user;
        $this->blockControl = $data->blockControl;
        $this->guid = $data->guid ?? '';
        return $this;
    }
}
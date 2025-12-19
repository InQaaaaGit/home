<?php

namespace local_cdo_rpd\DTO\RPD;

use tool_cdo_config\request\DTO\base_dto;

class save_rpd extends base_dto
{

    //public string $code;
    public string $status;
    public string $error;
    public string $guid;

    protected function get_object_name(): string
    {
        return 'save';
    }

    public function build(object $data): base_dto
    {
        //$this->code = $data->code;
        $this->status = $data->status;
        $this->guid = $data->guid;
        $this->error = $data->error ?? '';
        return $this;
    }
}
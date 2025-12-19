<?php

namespace local_cdo_order_documents\DTO;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class certificates_list_dto extends base_dto
{
    public string $id;
    public string $name;
    public string $type;
    public string $status;
    public string $comment;
    public ?string $date_certificate;

    protected function get_object_name(): string
    {
        return "document_structure";
    }

    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->name = $data->name;
        $this->type = $data->type;
        $this->status = $data->status;
        $this->comment = $data->comment;
        $this->date_certificate = $data->date_certificate;


        return $this;
    }
}

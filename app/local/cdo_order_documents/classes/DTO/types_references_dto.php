<?php
namespace local_cdo_order_documents\DTO;
use tool_cdo_config\request\DTO\base_dto;

class types_references_dto extends base_dto
{
    public string $id;
    public string $name;

    protected function get_object_name(): string
    {
        return "types_references";
    }

    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->name = $data->name;

        return $this;
    }
}
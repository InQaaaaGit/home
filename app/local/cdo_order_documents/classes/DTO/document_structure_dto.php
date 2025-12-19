<?php

namespace local_cdo_order_documents\DTO;

use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class document_structure_dto extends base_dto
{
    public string $document_type_id;
    public string $name;
    public response_dto $fields;
    public ?string $description;


    protected function get_object_name(): string
    {
        return "document_structure";
    }

    /**
     * @throws cdo_type_response_exception
     */
    public function build(object $data): base_dto
    {
      
        $this->document_type_id = $data->document_type_id;
        $this->name = $data->name;
        if (property_exists($data, 'description'))
        $this->description = $data->description;
        $this->fields = $data->fields
            ? response_dto::transform(document_structure_fields_dto::class, $data->fields)
            : [];

        return $this;
    }
}

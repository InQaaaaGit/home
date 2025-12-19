<?php

namespace local_cdo_order_documents\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class document_structure_fields_dto extends base_dto
{
    public string $name;
    public string $description;
    public string $type;
    public int $order;
    public ?response_dto $list_options;
    public ?string $value;

    protected function get_object_name(): string
    {
        return "fields";
    }

    /**
     * @param object $data
     * @return base_dto
     * @throws coding_exception
     * @throws cdo_type_response_exception
     */
    public function build(object $data): base_dto
    {
        $this->name = $data->name;
        $this->value = $data->value;
        $this->description = $data->description;
        $this->type = $data->type;
        $this->order = $data->order;
        if (property_exists($data, 'list_options'))
            if (count($data->list_options) > 0)
                $this->list_options = $data->list_options
                    ? response_dto::transform(list_options_dto::class, $data->list_options)
                    : [];

        return $this;
    }
}

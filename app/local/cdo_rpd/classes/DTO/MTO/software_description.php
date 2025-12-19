<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class software_description extends base_dto
{
    public array|response_dto $description;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'software_description';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->description = $data->description
            ? response_dto::transform(
                software_description_element::class,
                $data->description
            )
            : [];

        return $this;
    }
}
<?php

namespace local_cdo_rpd\DTO\MTO;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class structures_data extends base_dto
{
    public array|response_dto $room;
    public array|response_dto $building;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'structures_data';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->building = $data->building
            ? response_dto::transform(
                building::class,
                $data->building
            )
            : [];
        $this->room =  $data->room
            ? response_dto::transform(
                building::class,
                $data->room
            )
            : [];
        return $this;
    }
}
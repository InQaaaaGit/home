<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class books extends base_dto
{

    public response_dto|array $mainSelected;
    public response_dto|array $additionalSelected;
    public response_dto|array $methodicalSelected;

    protected function get_object_name(): string
    {
        return 'books';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->mainSelected = $data->mainSelected ?
            response_dto::transform(
                main_selected::class,
            $data->mainSelected
        ) : [];
        $this->additionalSelected = $data->additionalSelected ?
            response_dto::transform(
                main_selected::class,
            $data->additionalSelected
        ) : [];
        $this->methodicalSelected = $data->methodicalSelected ?
            response_dto::transform(
                main_selected::class,
            $data->methodicalSelected
        ) : [];
        return $this;
    }
}
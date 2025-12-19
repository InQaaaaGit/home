<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class fos extends base_dto
{

    public string $id;
    public ?response_dto $selectedValue;
    public string $questionDescription;
    public int $sort;

    protected function get_object_name(): string
    {
        return 'fos';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->selectedValue = $data->selectedValue ?
            response_dto::transform(
                competencies::class,
                $data->selectedValue
            ) : null;
        $this->questionDescription = $data->questionDescription;
        $this->sort = $data->sort;
        return $this;
    }
}
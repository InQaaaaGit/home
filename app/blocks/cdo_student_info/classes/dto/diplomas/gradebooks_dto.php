<?php

namespace block_cdo_student_info\dto\diplomas;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class gradebooks_dto extends base_dto
{

    /**
     * @var array|response_dto
     */
    public response_dto $diplomas;
    public string $id;
    public string $name;

    protected function get_object_name(): string
    {
        return 'gradebooks_dto';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->name = $data->name;
        $this->diplomas = $data->diplomas
            ? response_dto::transform(diplomas_dto::class, $data->diplomas)
            : [];
        return $this;
    }
}
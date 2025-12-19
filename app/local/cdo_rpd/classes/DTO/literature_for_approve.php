<?php

namespace local_cdo_rpd\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class literature_for_approve extends base_dto
{

    public ?string $comment;
    public ?string $status;
    public ?bool $result;
    public response_dto|array $literature;

    protected function get_object_name(): string
    {
        return 'literature_for_approve';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->result = $data->result ?? false;
        $this->comment = $data->comment;
        $this->status = $data->status;
        $this->literature = $data->literature
            ? response_dto::transform(
                literature_list::class,
                $data->literature
            )
            : [];

        return $this;
    }
}
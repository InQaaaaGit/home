<?php

namespace local_cdo_academic_progress\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class gradebook_dto extends base_dto
{

    public response_dto|array $progress;
    public string $gradebook;
    public bool $attestation_not_empty;
    public string $order;

    protected function get_object_name(): string
    {
        return "universal_dto";
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {

        $this->progress = $data->progress
            ? response_dto::transform(academic_progress_dto::class, $data->progress)
            : [];
        $this->attestation_not_empty = !empty($this->progress);
        $this->gradebook = $data->gradebook;
        $this->order = $data->order;

        return $this;
    }
}
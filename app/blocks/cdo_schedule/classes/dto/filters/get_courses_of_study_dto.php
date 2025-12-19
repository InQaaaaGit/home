<?php

namespace block_cdo_schedule\dto\filters;

use tool_cdo_config\request\DTO\base_dto;

class get_courses_of_study_dto extends base_dto
{
    public ?bool $error;
    public ?string $message;
    public ?string $id;
    public ?string $name;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'get_courses_of_study_dto';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->error = $data->error ?? false;
        $this->message = $data->message ?? '';
        $this->id = $data->id ?? '';
        $this->name = $data->name ?? '';

        return $this;
    }
}
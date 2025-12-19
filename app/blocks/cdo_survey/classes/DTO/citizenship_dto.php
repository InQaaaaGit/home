<?php

namespace block_cdo_survey\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class citizenship_dto extends base_dto {

    public ?string $name;
    public ?int $value;

    /**
     * @param object $data
     * @return base_dto
     */
    public function build(object $data): base_dto {
        $this->name = $data->name ?? null;
        $this->value = $data->value ?? null;

        return $this;
    }

    protected function get_object_name(): string {
        return "citizenship";
    }
}

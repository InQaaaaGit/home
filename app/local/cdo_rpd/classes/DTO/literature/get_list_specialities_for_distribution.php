<?php

namespace local_cdo_rpd\DTO\literature;

use local_cdo_rpd\DTO\literature_list;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class get_list_specialities_for_distribution extends base_dto
{
    public string $id;
    public string $direction;
    public string|response_dto $librarian;
    public string $status;
    public string $code;
    public string $trainingLevel;
    public string $educationLevels;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'get_list_specialities_for_distribution';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id;
        $this->direction = $data->direction;
        $this->librarian = $data->librarian
            ? response_dto::transform(
                librarian::class,
                $data->librarian
            )
            : '';
        $this->status = $data->status;
        $this->code = $data->code;
        $this->trainingLevel = $data->trainingLevel;
        $this->educationLevels = $data->educationLevels;
        return $this;
    }
}
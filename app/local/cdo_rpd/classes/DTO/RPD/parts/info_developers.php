<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class info_developers extends base_dto
{
    public response_dto|array $mainDeveloper;
    public response_dto|array $coDevelopers;
    public array|response_dto $allDisciplineDevelopers;

    protected function get_object_name(): string
    {
        return '';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->mainDeveloper = $data->mainDeveloper ?
            response_dto::transform(
                info_developer::class,
                $data->mainDeveloper
            ) : [];
         $this->coDevelopers = $data->coDevelopers ?
            response_dto::transform(
                info_developer::class,
                $data->coDevelopers
            ) : [];
         $this->allDisciplineDevelopers = $data->allDisciplineDevelopers ?
            response_dto::transform(
                info_developer::class,
                $data->allDisciplineDevelopers
            ) : [];

        return $this;
    }
}
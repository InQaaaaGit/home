<?php

namespace local_cdo_education_plan\DTO;

use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class education_plan_dto extends base_dto
{
    public response_dto $CurriculumEntries;
    public array $AttachmentList;
    public string $EduPlanNumber;
    public array $WebLinksEducationalProgram;
    protected function get_object_name(): string
    {
        return "education_plan";
    }

    /**
     * @param object $data
     * @return $this
     * @throws cdo_config_exception
     */
    public function build(object $data): base_dto
    {
        $this->EduPlanNumber = $data->EduPlanNumber ?? null;
        $this->AttachmentList = $data->AttachmentList ?? [];
        $this->WebLinksEducationalProgram = $data->WebLinksEducationalProgram ?? [];
        $this->CurriculumEntries = $data->CurriculumEntries
            ? response_dto::transform(curriculum_entry_dto::class, $data->CurriculumEntries)
            : null;
        return $this;
    }
}
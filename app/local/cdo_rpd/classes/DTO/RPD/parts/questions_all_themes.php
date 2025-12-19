<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class questions_all_themes extends base_dto
{

    public string $id;
    public string $questionDescription;
    public response_dto|array $selectedValue;
    public ?string $questionAnswers;

    protected function get_object_name(): string
    {
        return 'qat';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->id = $data->id ?? '';
        $this->selectedValue = $data->selectedValue ?
            response_dto::transform(
                competencies::class,
                $data->selectedValue
            ) : [];
        $this->questionDescription = $data->questionDescription ?? '';
        $this->questionAnswers = $data->questionAnswers ?? '';

        return $this;
    }
}
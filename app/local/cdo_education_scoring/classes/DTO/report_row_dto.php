<?php

namespace local_cdo_education_scoring\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class report_row_dto extends base_dto
{
    public int $survey_id;
    public string $survey_title;
    public string $user_fullname;
    public string $teacher_fullname;
    public string $discipline_id;
    public string $question_text;
    public string $response_value;
    public int $timecreated;

    protected function get_object_name(): string
    {
        return "report_row";
    }

    public function build(object $data): base_dto
    {
        $this->survey_id = (int)($data->survey_id ?? 0);
        $this->survey_title = (string)($data->survey_title ?? '');
        $this->user_fullname = (string)($data->user_fullname ?? '');
        $this->teacher_fullname = (string)($data->teacher_fullname ?? '');
        $this->discipline_id = (string)($data->discipline_id ?? '');
        $this->question_text = (string)($data->question_text ?? '');
        $this->response_value = (string)($data->response_value ?? '');
        $this->timecreated = (int)($data->timecreated ?? 0);

        return $this;
    }
}


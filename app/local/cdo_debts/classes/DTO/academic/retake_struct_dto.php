<?php

namespace local_cdo_debts\DTO\academic;

use coding_exception;

use local_cdo_academic_progress\DTO\directory_dto;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class retake_struct_dto extends base_dto
{

    public ?string $who;
    public string $subject;
    public string $body;
    public ?string $file;
    public string $date;
    public response_dto $status;
    public string $date_for_retake;
    public ?string $date_for_retake_convert;
    public response_dto $document_source;
    public ?response_dto $teachers;
    public ?string $student;
    public ?string $gradebook;
    public ?string $discipline;
    public ?string $commentary;
    public ?string $student_id;

    protected function get_object_name(): string
    {
        return "retake_struct_dto";
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->who = $data->who ?? null;
        $this->subject = $data->subject;
        $this->body = $data->body;;
        $this->file = $data->file;
        $this->date = $data->date;
        $this->status = $data->status
            ? response_dto::transform(directory_dto::class, $data->status)
            : null;
        $this->date_for_retake = $data->date_for_retake;
        $this->date_for_retake_convert = $data->date_for_retake_convert;
        $this->document_source = $data->document_source
            ? response_dto::transform(directory_dto::class, $data->document_source)
            : null;
        $this->teachers = $data->teachers
            ? response_dto::transform(options_dto::class, $data->teachers)
            : null;
        $this->student = $data->student ?? '';
        $this->gradebook = $data->gradebook ?? '';
        $this->discipline = $data->discipline ?? '';
        $this->commentary = $data->commentary ?? '';
        $this->student_id = $data->student_id ?? '';
        return $this;
    }
}
<?php

namespace local_cdo_debts\DTO\academic;

use local_cdo_academic_progress\DTO\directory_dto;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class academic_debts_dto extends base_dto
{
    public string $academic_plan;
    public string $semester;
    public response_dto $study_load_type;
    public response_dto $discipline;
    public string $academic_plan_type_record;
    public string $student_record_book;
    public string $grade;
    public string $attempt;
    public string $grade_short_name;
    public string $grade_color_code;
    public string $mark;
    public response_dto $education_form;
    public response_dto $faculty;
    public string $academic_year;
    public string $cohort;
    public response_dto $tuition_fee;
    public response_dto $document_source;

    protected function get_object_name(): string
    {
        return "academic_debts";
    }

    /**
     * @param object $data
     * @return base_dto
     * @throws cdo_config_exception
     */
    public function build(object $data): base_dto
    {
        $this->academic_plan = $data->academic_plan ?? null;
        $this->semester = $data->semester ?? null;
        $this->study_load_type = $data->study_load_type
            ? response_dto::transform(study_load_type_dto::class, $data->study_load_type)
            : null;
        $this->discipline = $data->discipline
            ? response_dto::transform(directory_dto::class, $data->discipline)
            : null;
        $this->academic_plan_type_record = $data->academic_plan_type_record ?? null;
        $this->student_record_book = $data->student_record_book ?? null;
        $this->grade = $data->grade ?? null;
        $this->attempt = $data->attempt ?? null;
        $this->grade_short_name = $data->grade_short_name ?? null;
        $this->grade_color_code = $data->grade_color_code ?? null;
        $this->mark = $data->mark ?? null;
        $this->education_form = $data->education_form
            ? response_dto::transform(directory_dto::class, $data->education_form)
            : null;
        $this->faculty = $data->faculty
            ? response_dto::transform(directory_dto::class, $data->faculty)
            : null;
        $this->academic_year = $data->academic_year ?? null;
        $this->cohort = $data->cohort ?? null;
        $this->tuition_fee = $data->tuition_fee
            ? response_dto::transform(directory_dto::class, $data->tuition_fee)
            : null;

        $this->document_source = $data->document_source
            ? response_dto::transform(directory_dto::class, $data->document_source)
            : null;
        return $this;
    }
}
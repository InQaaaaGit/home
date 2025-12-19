<?php

namespace local_cdo_certification_sheet\DTO;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class certification_sheet_dto extends base_dto {

	public ?string $name_plan;
	public ?string $group;
	public ?string $profile;
	public ?string $semester;
	public ?string $division;
	public ?string $form_education;
	public string $level_education;
	public ?string $specialty;
	public ?string $course;
	public string $guid;
	public string $discipline;
	public string $type_control;
	public ?string $type_control_code;
	public string $date;
	public string $name_sheet;
	public string $type;
	public string $type_code;
	public bool $useBRS;
	public bool $useDivisionRules1c;

	public response_dto $students;
	public response_dto $teachers;
	public response_dto $system_grades;
    public string $dateJS;

    /**
     * @param object $data
     * @return base_dto
     * @throws coding_exception
     * @throws cdo_type_response_exception
     */
	public function build(object $data): base_dto {
		$this->name_plan = $data->rep ?? null;
		$this->useDivisionRules1c = $data->useDivisionRules1c ?? false;
		$this->useBRS = $data->useBRS ?? false;
		$this->group = $data->group ?? null;
		$this->profile = $data->profile ?? null;
		$this->semester = $data->semester ?? null;
		$this->division = $data->division ?? null;
		$this->form_education = $data->formstudy ?? null;
		$this->level_education = $data->leveledu ?? '';
		$this->specialty = $data->speacility ?? null;
		$this->course = $data->course ?? null;
		$this->guid = $data->GUID ?? null;
		$this->discipline = $data->discipline ?? null;
		$this->type_control = $data->ControlType ?? null;
		$this->date = $data->date ?? null;
		$this->name_sheet = $data->NameSheet ?? null;
		$this->type = $data->type ?? null;
		$this->type_code = $data->typeCode ?? null;
		$this->type_control_code = $data->ControlTypeCode ?? null;
		$this->dateJS = $data->dateJS ?? null;
		$this->hours = $data->hours ?? '';
		$this->students = $data->students
			? response_dto::transform(student_certification_sheet_dto::class, $data->students)
			: null;
		$this->teachers = $data->teachers
			? response_dto::transform(teacher_certification_sheet_dto::class, $data->teachers)
			: null;
		$this->system_grades = $data->systemgrade
			? response_dto::transform(system_grade_certification_sheet_dto::class, $data->systemgrade)
			: null;

		return $this;
	}

	protected function get_object_name(): string {
		return "certification_sheet";
	}
}

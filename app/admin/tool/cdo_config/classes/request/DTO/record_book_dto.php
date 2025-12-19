<?php

namespace tool_cdo_config\request\DTO;

use tool_cdo_config\exceptions\cdo_config_exception;

final class record_book_dto extends base_dto {

	public string $id;
	public string $name;
	public int $education_year;
	public response_dto $cohort;
	public response_dto $faculty;
	public response_dto $level;
	public response_dto $speciality;
	public response_dto $specialisation;
	public response_dto $education_form;
	public response_dto $tuition_fee;
	public string $education_start_date;
	public string $education_estimated_end_date;

	/**
	 * @return string
	 */
	protected function get_object_name(): string {
		return "record_book";
	}

	/**
	 * @param object $data
	 * @return $this
	 * @throws cdo_config_exception
	 */
	public function build(object $data): self {
		$this->id = $data->id ?? null;
		$this->name = $data->name ?? null;
		$this->education_year = $data->education_year ?? null;
		$this->education_start_date = $data->education_start_date ?? null;
		$this->education_estimated_end_date = $data->education_estimated_end_date ?? null;

		$this->cohort = $data->cohort ? response_dto::transform(directory_dto::class, $data->cohort) : null;
		$this->faculty = $data->faculty ? response_dto::transform(directory_dto::class, $data->faculty) : null;
		$this->level = $data->level ? response_dto::transform(directory_dto::class, $data->level) : null;
		$this->speciality = $data->speciality ? response_dto::transform(directory_dto::class, $data->speciality) : null;
		$this->specialisation = $data->specialisation ? response_dto::transform(directory_dto::class, $data->specialisation) : null;
		$this->education_form = $data->education_form ? response_dto::transform(directory_dto::class, $data->education_form) : null;
		$this->tuition_fee = $data->tuition_fee ? response_dto::transform(directory_dto::class, $data->tuition_fee) : null;
		return $this;
	}
}
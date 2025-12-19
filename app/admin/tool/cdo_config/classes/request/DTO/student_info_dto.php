<?php

namespace tool_cdo_config\request\DTO;

use tool_cdo_config\exceptions\cdo_config_exception;

final class student_info_dto extends base_dto {

	public string $id;
	public string $presentation;
	public string $birth_date;
	public string $surname;
	public string $name;
	public string $middlename;
	public ?string $email;
	public array $phone;
	public string $city;
	public response_dto $student_record_books;

	/**
	 * @param object $data
	 * @return $this
	 * @throws cdo_config_exception
	 */
	public function build(object $data): self {
		$this->id = $data->id ?? null;
		$this->presentation = $data->presentation ?? null;
		$this->birth_date = $data->birth_date ?? null;
		$this->surname = $data->surname ?? null;
		$this->name = $data->name ?? null;
		$this->middlename = $data->middlename ?? null;
		$this->email = $data->email ?? null;
		$this->phone = $data->phone ?? null;
		$this->city = $data->city ?? null;
		$this->student_record_books = $data->student_record_books
			? response_dto::transform(record_book_dto::class, $data->student_record_books)
			: null;
		return $this;
	}

	/**
	 * @return string
	 */
	protected function get_object_name(): string {
		return "student_info";
	}
}
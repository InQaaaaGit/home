<?php

namespace local_cdo_certification_sheet\services;

use coding_exception;
use local_cdo_certification_sheet\output\select_grade\renderable as select_grade;
use tool_cdo_config\exceptions\cdo_config_exception;

defined('MOODLE_INTERNAL') || die();

class grade_element implements i_sheet_component {

	private string $sheet_type_code;
	private string $sheet_guid;

	private string $full_name;
	private string $grade_book;
	private string $guid;
	private string $grade;
	private string $teacher_code;
	private string $short_name;
	private string $color;
	private string $teacher_full_name;

	private array $system_grades;
	private array $teachers;

	public function __construct(array $options) {
		$this->full_name = $options['student']['full_name'];
		$this->grade_book = $options['student']['grade_book'];
		$this->guid = $options['student']['guid'];
		$this->grade = $options['student']['grade'];
		$this->teacher_code = $options['student']['teacher_code'];
		$this->short_name = $options['student']['short_name'];
		$this->color = $options['student']['color'];
		$this->teacher_full_name = $options['student']['teacher_full_name'];

		$this->system_grades = $options['system_grades'];

		$this->teachers = $options['teachers'];
		$this->sheet_type_code = $options['sheet_type_code'];
		$this->sheet_guid = $options['sheet_guid'];
	}

	/**
	 * @return array
	 * @throws cdo_config_exception
	 */
	public function get_data(): array {
		return [
			'full_name' => $this->full_name,
			'grade_book' => $this->grade_book,
			'guid' => $this->guid,
			'grade' => $this->grade,
			'teacher_code' => $this->teacher_code,
			'short_name' => $this->short_name,
			'color' => $this->color,
			'teacher_full_name' => $this->teacher_full_name,
			'student_system_grades' => $this->build_info()
		];
	}

	/**
	 * @return array
	 * @throws coding_exception
	 */
	protected function get_system_grades(): array {
		$content = [];
		$content[] = $this->get_empty_grade();
		foreach ($this->system_grades as $item) {
			$_builder_grades = $item;
			$_builder_grades['selected'] = $item['guid'] === $this->grade;
			if ($_builder_grades['selected']) {
				$content[0]['selected'] = false;
			}
			$content[] = $_builder_grades;
		}
		return $content;
	}

	protected function is_edit(): bool {
		global $USER;
		//TODO изменить на проде на $USER->id
		$user_id = $USER->id;

		$_chairman = false;
		$_teacher = false;
		$_editor_teacher = false;

		foreach ($this->teachers as $teacher) {
			if ((int)$teacher['chairman'] && (int) $teacher['user_id'] === (int) $user_id ?? $USER->id) {
				$_chairman = true;
			}

			if ((int) $teacher['user_id'] === (int) $user_id ?? $USER->id) {
				$_teacher = true;
			}

			if (!(int) $this->teacher_code || (int) $this->teacher_code === (int) $user_id ?? $USER->id) {
				$_editor_teacher = true;
			}
		}

		//Ведомость с председателем
		if (list_sheet::is_chairman_sheet() === $this->sheet_type_code) {
			return $_chairman;
		}

		return $_teacher && $_editor_teacher;
	}

	/**
	 * @return array
	 * @throws coding_exception
	 */
	protected function get_empty_grade(): array {
		return [
			"id" => "00000000-0000-0000-0000-000000000000",
			"value" => get_string('grade_element_not_grades', 'local_cdo_certification_sheet'),
			"grade" => "",
			"guid" => "00000000-0000-0000-0000-000000000000",
			"short_name" => "",
			"color" => "1",
			"selected" => true,
		];
	}

	/**
	 * @return string
	 * @throws cdo_config_exception
	 */
	public function build_info(): string {
		global $PAGE;

		try {

			$guids = [
				'grade_guid' => $this->grade,
				'student_guid' => $this->guid,
				'sheet_guid' => $this->sheet_guid,
			];

			$select_grade = new select_grade(
				$this->get_system_grades(),
				$guids,
				$this->is_edit()
			);

			return $PAGE
				->get_renderer('local_cdo_certification_sheet', 'select_grade')
				->render($select_grade);
		} catch (coding_exception $e) {
			throw new cdo_config_exception(2003, $e->getMessage());
		}
	}
}

<?php

namespace block_cdo_seamless_transition\forms;

use coding_exception;
use moodleform;

global $CFG;

require_once($CFG->libdir . '/formslib.php');

class setting_form extends moodleform {

	private string $component_string = 'block_cdo_seamless_transition';

	/**
	 * @return void
	 * @throws coding_exception
	 */
	protected function definition(): void {
		$this->build_yurayt();
		$this->build_lan();
		$this->build_ipr_books();
		$this->build_znanium();
		$this->build_consultant_student();
		$this->build_ibooks();

		$this->add_action_buttons();
	}

	public function validation($data, $files) {
		return (new validation_form($data))->valid();
	}

    private function build_ibooks() {
        $this->_form->addElement(
            'header',
            "ibooks_header",
            get_string('ibooks_name', $this->component_string)
        );
        $this->add_element_checkbox('ibooks_show');
        $this->add_element_text('ibooks_token', 'ibooks_show');
        $this->add_element_text('ibooks_id', 'ibooks_show');
        $this->add_element_text('ibooks_domain', 'ibooks_show');
    }

	/**
	 * @return void
	 * @throws coding_exception
	 */
	private function build_yurayt(): void {
		//Заголовок блока
		$this->_form->addElement(
			'header',
			"yurayt_main",
			get_string('yurayt_main', $this->component_string)
		);

		$this->add_element_checkbox('yurayt_show');
		$this->add_element_text('yurayt_token', 'yurayt_show');
		$this->add_element_text('yurayt_pid', 'yurayt_show');
	}

	/**
	 * @return void
	 * @throws coding_exception
	 */
	private function build_lan(): void {
		//Заголовок блока
		$this->_form->addElement(
			'header',
			"lan_main",
			get_string('lan_main', 'block_cdo_seamless_transition')
		);

		$this->add_element_checkbox('lan_show');
		$this->add_element_text('lan_token', 'lan_show');
	}

	/**
	 * @return void
	 * @throws coding_exception
	 */
	private function build_ipr_books(): void {
		//Заголовок блока
		$this->_form->addElement(
			'header',
			"ipr_books_main",
			get_string('ipr_books_main', 'block_cdo_seamless_transition')
		);
		$this->add_element_checkbox('ipr_books_show');
		$this->add_element_text('ipr_books_token', 'ipr_books_show');
		$this->add_element_text('ipr_books_domain', 'ipr_books_show');
	}

	/**
	 * @return void
	 * @throws coding_exception
	 */
	private function build_znanium(): void {
		//Заголовок блока
		$this->_form->addElement(
			'header',
			"znanium_main",
			get_string('znanium_main', 'block_cdo_seamless_transition')
		);
		$this->add_element_checkbox('znanium_show');
		$this->add_element_text('znanium_token', 'znanium_show');
		$this->add_element_text('znanium_domain', 'znanium_show');
	}

	/**
	 * @return void
	 * @throws coding_exception
	 */
	private function build_consultant_student(): void {
		//Заголовок блока
		$this->_form->addElement(
			'header',
			"consultant_student_main",
			get_string('consultant_student_main', 'block_cdo_seamless_transition')
		);

		$this->add_element_checkbox('consultant_student_show');
		$this->add_element_text('consultant_student_organization', 'consultant_student_show');
		$this->add_element_text('consultant_student_contract', 'consultant_student_show');
	}

	/**
	 * @param string $name
	 * @param string $hide_element
	 * @return void
	 * @throws coding_exception
	 */
	private function add_element_text(string $name, string $hide_element): void {
		$this->_form->addElement('text', $name, get_string($name, $this->component_string), ['size' => 50]);
		$this->_form->addHelpButton($name, $name, $this->component_string);
		$this->_form->setType($name, PARAM_TEXT);
		$this->_form->hideIf($name, $hide_element);
	}

	/**
	 * @param string $name
	 * @return void
	 * @throws coding_exception
	 */
	private function add_element_checkbox(string $name): void {
		$this->_form->addElement('checkbox', $name, get_string($name, $this->component_string));
	}
}
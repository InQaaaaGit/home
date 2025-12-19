<?php

namespace local_cdo_certification_sheet\forms;

use coding_exception;
use moodleform;

global $CFG;

require_once($CFG->libdir . '/formslib.php');

class setting_form extends moodleform {

	/**
	 * @return void
	 * @throws coding_exception
	 */
	protected function definition(): void {
		//Заголовок блока с основной информацией
		$this->_form->addElement(
			'header',
			"setting_form_main",
			get_string('setting_form_main', 'local_cdo_certification_sheet')
		);

		$this->_form->addElement(
			'text',
			"setting_form_code",
			get_string("setting_form_code", "local_cdo_certification_sheet"),
			['size' => 50]);

		$this->_form->addHelpButton("setting_form_code", "setting_form_code", "local_cdo_certification_sheet");
		$this->_form->setType("setting_form_code", PARAM_TEXT);
		$this->_form->addRule(
			"setting_form_code",
			get_string('required_param', "local_cdo_certification_sheet"),
			'required'
		);

		$this->add_action_buttons();
	}

}
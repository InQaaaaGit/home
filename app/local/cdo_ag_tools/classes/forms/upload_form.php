<?php

namespace local_cdo_ag_tools\forms;

use moodleform;

class upload_form extends moodleform
{
    // Add elements to form.
    public function definition(): void
    {
        // A reference to the form is stored in $this->form.
        // A common convention is to store it in a variable, such as `$mform`.
        $mform = $this->_form; // Don't forget the underscore!

        // Add elements to your form.
        $mform->addElement(
            'filepicker',
            'grade_report',
            get_string('grade_report', 'local_cdo_ag_tools'),
            null,
            [
                'accepted_types' => 'xlsx',
            ]
        );
        // Set type of element.
        $mform->setType('grade_report', PARAM_NOTAGS);

        // Default value.
        $mform->setDefault('grade_report', 'Upload file');
        $this->add_action_buttons();
    }

    // Custom validation should be added here.
    function validation($data, $files)
    {
        return [];
    }
}
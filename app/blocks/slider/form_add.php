<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once("$CFG->libdir/formslib.php");

class simplehtml_form extends moodleform
{
    //Add elements to form
    private $sections = [0 => "Техническая библиотека", 1 => "Учебные центры Компании"];

    private function getSections()
    {
        return [
            0 => get_string('slider_select_lib', 'block_slider'),
            1 => get_string('slider_select_company', 'block_slider')
        ];
    }

    public function definition()
    {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!


        $mform->addElement('header', 'slide_heading',
            get_string('slider_slide_heading', 'block_slider')
        );

        $mform->addElement('select', 'section',
            get_string('slider_slide_section', 'block_slider'), $this->getSections()
        );

        $mform->hideIf('text_company', 'section', 'eq', '0');
        $mform->hideIf('email', 'section', 'eq', '0');
        $mform->hideIf('telephone', 'section', 'eq', '0');


        $mform->addElement('text', 'text_company',
            get_string('slider_slide_text_company', 'block_slider')
        );
        $mform->setType('text_company', PARAM_TEXT);

        $mform->addElement('text', 'email',
            get_string('slider_slide_email', 'block_slider')
        );
        $mform->setType('email', PARAM_TEXT);

        $mform->addElement('text', 'telephone',
            get_string('slider_slide_telephone', 'block_slider')
        );
        $mform->setType('telephone', PARAM_TEXT);


        $mform->setType('slide_text', PARAM_TEXT);
        $mform->addElement('text', 'slide_header',
            get_string('slider_slide_head', 'block_slider')
        );

        $mform->setType('slide_header', PARAM_TEXT);
        $mform->addElement('textarea', 'slide_text',
            get_string('slider_slide_text', 'block_slider')
        );

        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'id', 0);

        $mform->setType('slide_text', PARAM_TEXT);
        $mform->addElement('filepicker', 'slide_file', get_string('file'), null,
            array('maxbytes' => 20, 'accepted_types' => '.jpg .png .jpeg'));

       // $mform->addRule('slide_file', "Добавьте файл", "required");
        $this->add_action_buttons();

    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
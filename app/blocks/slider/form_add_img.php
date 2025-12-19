<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once("$CFG->libdir/formslib.php");
require_once("$CFG->dirroot/course/lib.php");

class simplehtml_form extends moodleform
{
    private function getCategories()
    {
        $list = [];
        foreach (core_course_category::get_all() as $item) {
            $list[$item->id] = $item->name;
        }
        return $list;
    }

    public function definition()
    {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!


        $mform->addElement('header', 'slide_heading',
            get_string('slider_slide_heading', 'block_slider')
        );

        $mform->setType('cat_id', PARAM_INT);
        $mform->addElement('hidden', 'cat_id', 0);


        $mform->addElement('filemanager', 'attachments', get_string('courseoverviewfiles', 'core'), null,
            array('subdirs' => 0, 'maxbytes' => 200, 'areamaxbytes' => 10485760, 'maxfiles' => 50,
                'accepted_types' => array('image')));

        $this->add_action_buttons();

    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
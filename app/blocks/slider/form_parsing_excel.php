<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once("$CFG->libdir/formslib.php");

class parsing_form extends moodleform
{
    private function getCourses(): array
    {
        $courses = get_courses();


        $preparedCourses = [];
        $preparedCourses[0] = '';
        foreach ($courses as $course) {
            $cc = core_course_category::get($course->category)->name;
          #  var_dump($cc);
            $preparedCourses[$course->id] = $cc . " / " . $course->fullname;
        }
        return $preparedCourses;
    }

    public function definition()
    {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('filepicker', 'parsingFile', get_string('file'), null,
            array('maxbytes' => 200, 'accepted_types' => '.csv'));
        $courseList = $this->getCourses();
        $modList = [];
        $modList[0] = ''; //
        foreach ($courseList as $course => $value) {
            if ($course !== 0) {
                $cms = get_fast_modinfo($course);
                foreach ($cms->get_cms() as $cm) {
                    $modList[$cm->id] = $cm->name;
                }
            }
        }

        $mform->addElement(
            'select',
            'modList',
            'Выберите модуль',
            $modList
        );
        $mform->hideif("modList", "courseList", "eq", 0);
        $mform->addElement(
            'select',
            'courseList',
            'Выберите курс',
            $this->getCourses()
        );
        $mform->setType('modList', PARAM_INT);


        # $mform->addRule('modList', "haha", "required");
        #$mform->addElement('button', 'upload_cm', "Загрузить");

        $this->add_action_buttons();

    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
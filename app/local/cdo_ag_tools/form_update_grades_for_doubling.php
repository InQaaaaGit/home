<?php

use local_cdo_ag_tools\task\accumulate_grades;

require_once('../../config.php');
require_once($CFG->libdir . '/formslib.php');

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/cdo_ag_tools/init_update_grades_for_doubling.php');
$PAGE->set_title(get_string('update_grades_for_doubling_title', 'local_cdo_ag_tools'));
$PAGE->set_heading(get_string('update_grades_for_doubling_heading', 'local_cdo_ag_tools'));

class course_id_form extends moodleform
{
    public function definition(): void
    {
        $mform = $this->_form;

        $mform->addElement('text', 'courseid', get_string('course_id_label', 'local_cdo_ag_tools'), ['size' => '5']);
        $mform->setType('courseid', PARAM_INT);
        $mform->addRule('courseid', get_string('course_id_required', 'local_cdo_ag_tools'), 'required', null, 'client');
        $mform->addRule('courseid', get_string('course_id_numeric', 'local_cdo_ag_tools'), 'numeric', null, 'client');

        $mform->addElement('checkbox', 'use_sequence', get_string('use_sequence', 'local_cdo_ag_tools'));
        $mform->setType('use_sequence', PARAM_BOOL);
        $mform->setDefault('use_sequence', 0);

        $this->add_action_buttons(false, get_string('submit_button', 'local_cdo_ag_tools'));
    }
}

echo $OUTPUT->header();

$mform = new course_id_form();

if ($mform->is_cancelled()) {
} else if ($data = $mform->get_data()) {
    if ($data->use_sequence) {
        global $DB;
        try {
            $result = $DB->insert_record('cdo_ag_tools_accumulate', ["courseid" => $data->courseid]);
            \core\notification::info('Успешно!');
        } catch (Exception $e) {
            \core\notification::error( "Ошибка записи в БД. Возможно Дублирование");
        }

    } else {
        $course = get_course($data->courseid);
        $task = new accumulate_grades();
        $task->set_custom_data(['courseid' => $data->courseid]);
        $task->set_component('local_cdo_ag_tools');
        \core\task\manager::queue_adhoc_task($task, true);
        echo $OUTPUT->notification(
            get_string('notify_success', 'local_cdo_ag_tools') . $course->fullname,
            \core\output\notification::NOTIFY_INFO,
            false
        );
    }

} else {
    $mform->display();
}

echo $OUTPUT->footer();

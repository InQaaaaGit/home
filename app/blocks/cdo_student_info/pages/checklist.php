<?php

use block_cdo_student_info\output\checklist\renderable as checklist_renderable;

require_once(__DIR__."/../../../config.php");
require_login();
global $PAGE, $OUTPUT;
$title = get_string('checklist_link','block_cdo_student_info');
$systemcontext = context_system::instance();
$url = '/blocks/cdo_student_info/pages/checklist_link.php';
$PAGE->set_context($systemcontext);
$PAGE->set_url($url);
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->navbar->add(
    $title, new moodle_url($url)
);
echo $OUTPUT->header();
$render = $PAGE->get_renderer('block_cdo_student_info', 'checklist');
$html = $render->render(new checklist_renderable());
echo $html;

echo $OUTPUT->footer();
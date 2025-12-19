<?php

use block_cdo_student_info\output\health\renderable as renderableHealth;

require_once(__DIR__."/../../config.php");
require_login();

global $PAGE, $OUTPUT;
$title = get_string('my_health','block_cdo_student_info');
$systemcontext = context_system::instance();
require_capability('block/cdo_student_info:view_my_health', $systemcontext);
$url = '/blocks/cdo_student_info/my_health.php';
$PAGE->set_context($systemcontext);
$PAGE->set_url($url);
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->navbar->add(
    $title, new moodle_url($url)
);
echo $OUTPUT->header();
$render = $PAGE->get_renderer('block_cdo_student_info', 'health');
$html = $render->render(new renderableHealth());
echo $html;

echo $OUTPUT->footer();
<?php

use local_cdo_education_plan\output\education_plan\renderable as renderable_output;

require_once(__DIR__."/../../config.php");
require_login();
global $PAGE, $OUTPUT;
$title = get_string('pluginname','local_cdo_education_plan');
$systemcontext = context_system::instance();
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/cdo_education_plan/education_plan.php');
$PAGE->set_heading($title);
$PAGE->set_title($title);
echo $OUTPUT->header();
$render = $PAGE->get_renderer('local_cdo_education_plan', 'education_plan');

$html = $render->render(new renderable_output());
//$html = $render->render(new renderable_output());
echo $html;
echo $OUTPUT->footer();
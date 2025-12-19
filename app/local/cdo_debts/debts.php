<?php

use local_cdo_debts\output\debts\renderable as renderable_output;

require_once(__DIR__."/../../config.php");
require_login();
global $PAGE, $OUTPUT;
$type_render = optional_param("type", 0, PARAM_INT);
$title = get_string('pluginname','local_cdo_debts');
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url('/local/cdo_debts/debts.php');
$PAGE->set_heading($title);
$PAGE->set_title($title);
echo $OUTPUT->header();

$render = $PAGE->get_renderer('local_cdo_debts', 'debts');
$html = $render->render(new renderable_output($type_render));

echo $html;
echo $OUTPUT->footer();
<?php

use local_cdo_academic_progress\output\academic_progress\renderable;

require_once(__DIR__."/../../config.php");

require_login();
global $PAGE, $OUTPUT;
$title = get_string('pluginname','local_cdo_academic_progress');
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url('/local/cdo_academic_progress/academic_progress.php');
$PAGE->set_heading($title);
$PAGE->set_title($title);
echo $OUTPUT->header();
$s = has_capability('local/cdo_academic_progress:view', $systemcontext);
require_capability('local/cdo_academic_progress:view', $systemcontext);
$render = $PAGE->get_renderer('local_cdo_academic_progress', 'academic_progress');

try {
    $html = $render->render(new renderable());
    echo $html;
} catch (\core\exception\coding_exception $e) {
    html_writer::div('Ошибка при формировании страницы:' . $e->getMessage(), 'alert alert-danger');
}
/*$discipline_link = html_writer::link(new moodle_url('/local/cdo_ok/survey.php',
    [
        'discipline' => $integration,
        'discipline_name' => $value->discipline->name,
        'group_tab' => 0
    ]),
    'Оценить'
);*/
echo $OUTPUT->footer();
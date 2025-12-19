<?php

require('../../config.php');
require_login();
$title = get_string('modulename', 'mod_vuejsdemo');
$url = new moodle_url('/mod/vuejsdemo/view.php');
global $PAGE, $OUTPUT;
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');

$PAGE->requires->js_call_amd('local_cdo_academic_progress/app-lazy', 'init', [
]);

echo $OUTPUT->header();

echo <<<'EOT'
<div id="mod-vuejsdemo-app">
 <component></component>
</div>
EOT;

echo $OUTPUT->footer();

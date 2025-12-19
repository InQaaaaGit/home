<?php

use local_cdo_mto\output\academic_progress\renderable;

require_once(__DIR__."/../../config.php");

require_login();
global $PAGE, $OUTPUT;
$title = get_string('pluginname','local_cdo_mto');
$systemcontext = context_system::instance();
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/cdo_mto/mto.php');
$PAGE->set_heading($title);
$PAGE->set_title($title);



echo $OUTPUT->header();
echo '
<div id="main_app">
  <div class="spinner-border text-primary" role="status">
    <span class="sr-only"></span>
  </div>
</div>';
echo $OUTPUT->footer();

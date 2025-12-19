<?php
require_once(__DIR__ . "/../../../config.php");
require_login();
$plugin_name = 'local_cdo_ag_tools';
global $PAGE, $OUTPUT;
$title = get_string('settings_availability', $plugin_name); // Add translations here if needed.
$url = new moodle_url('/local/cdo_ag_tools/pages/settings_availability.php');
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url($url);
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->set_pagelayout('base');
$previewnode = $PAGE->navigation->add(
    $title,
    $url,
    navigation_node::TYPE_CONTAINER
);
$PAGE->requires->js_call_amd(
    $plugin_name."/app-lazy",
    'init',
    [

    ]
);
echo $OUTPUT->header();
echo
'<div id="main_app">
    <div class="spinner-border text-primary" role="status">
      <span class="sr-only"></span>
    </div>
    <availability></availability>
</div>';
echo $OUTPUT->footer();

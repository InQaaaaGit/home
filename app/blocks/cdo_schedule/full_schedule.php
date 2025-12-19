<?php
require_once(__DIR__ . "/../../config.php");
require_login();
global $PAGE, $OUTPUT;
$title = get_string('pluginname', 'block_cdo_schedule');
$url = new moodle_url('/blocks/cdo_schedule/full_schedule.php');
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url($url);
$PAGE->set_heading($title);
$PAGE->set_title($title);
$previewnode = $PAGE->navigation->add(
    $title,
    $url,
    navigation_node::TYPE_CONTAINER
);
global $CFG;
$pluginname = 'block_cdo_schedule';
$amd_build = $CFG->cachejs ? $pluginname."/full-schedule-prod-app-lazy" : $pluginname."/full-schedule-dev-app-lazy";
$PAGE->requires->js_call_amd(
    $amd_build,
    'init',
    [[
        'isStudent' => has_capability('block/cdo_schedule:viewstudentschedule', $systemcontext),
        'isTeacher' => has_capability('block/cdo_schedule:viewteacherschedule', $systemcontext)
    ]]
);
global $CFG;

echo $OUTPUT->header();
echo "<style>.v-select__selections input {
  display: none;

}</style>";
echo $OUTPUT->render_from_template('block_cdo_schedule/mini_app', []);
echo $OUTPUT->footer();

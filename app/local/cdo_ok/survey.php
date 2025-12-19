<?php

use local_cdo_ok\helper\helper;
use local_cdo_ok\output\vuetify\renderable as vuetify;

require(__DIR__ . "/../../config.php");

require_login();
global $PAGE, $OUTPUT, $CFG, $USER;
$plugin = 'local_cdo_ok';
$context = context_system::instance();
$PAGE->set_context($context);
$title = get_string('title_survey', $plugin);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url(new moodle_url('/local/cdo_ok/survey.php'));
$discipline = required_param('discipline', PARAM_TEXT);
$group_tab = required_param('group_tab', PARAM_INT);
$discipline_name = optional_param('discipline_name', '', PARAM_TEXT);
$PAGE->navbar->add(
    "Успеваемость", new moodle_url('/local/cdo_academic_progress')
);
$PAGE->navbar->add(
    $title, new moodle_url('/local/cdo_ok/survey.php')
);
$amd_build = $CFG->cachejs ? $plugin."/prod-app-lazy" : $plugin."/dev-app-lazy";


$params = [
    'years' => helper::get_years(),
    'reports' => helper::get_reports(),
    'app_type' => 'survey',
    'discipline' => $discipline_name,
    'discipline_code' => $discipline,
    'group_tab' => $group_tab
];

$PAGE->requires->js_call_amd(
    $amd_build,
    'init',
    [
        $params
    ]
);


echo $OUTPUT->header();
$rendererVuetify = $PAGE->get_renderer($plugin, 'vuetify');
try {
    echo $rendererVuetify->render(new vuetify());
} catch (coding_exception $e) {
}
echo $OUTPUT->footer();
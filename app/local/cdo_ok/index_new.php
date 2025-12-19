<?php

use local_cdo_ok\controllers\questions_controller;
use local_cdo_ok\helper\helper;
use local_cdo_ok\output\vuetify\renderable as vuetify;

require(__DIR__ . "/../../config.php");

require_login();
global $PAGE, $OUTPUT, $CFG, $USER;
//TODO check ROLE!!!
try {
    $plugin = 'local_cdo_ok';
    $context = context_system::instance();
    $PAGE->set_context($context);
    $plugin_name = get_string('title', 'local_cdo_ok');
    $PAGE->set_title($plugin_name);
    $PAGE->set_heading($plugin_name);
    $PAGE->set_url(new moodle_url('/local/cdo_ok/index.php'));
    $PAGE->navbar->add(
        $plugin_name, new moodle_url('/local/cdo_ok/index.php')
    );
    $params = [
        'years' => helper::get_years(),
        'reports' => helper::get_reports(),
        'app_type' => 'admin'
    ];
    $amd_build = $CFG->cachejs ? $plugin."/prod-app-lazy" : $plugin."/dev-app-lazy";

    $PAGE->requires->js_call_amd(
        $amd_build,
        'init',
        [
            $params
        ]
    );

} catch (coding_exception|moodle_exception $e) {
    var_dump($e);
}


echo $OUTPUT->header();

$rendererVuetify = $PAGE->get_renderer('local_cdo_ok', 'vuetify');
try {
    echo $rendererVuetify->render(new vuetify());
} catch (coding_exception $e) {
}
echo $OUTPUT->footer();
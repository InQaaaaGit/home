<?php

use local_cdo_ok\controllers\questions_controller;
use local_cdo_ok\helper\helper;
use local_cdo_ok\output\vuetify\renderable as vuetify;

require(__DIR__ . "/../../config.php");

require_login();
global $PAGE, $OUTPUT, $CFG, $USER;

try {
    $params = [];
    $url = '/local/cdo_ok/retakes.php';
    $context = context_system::instance();
    $PAGE->set_context($context);
    $plugin_name = get_string('academic_debts', 'local_cdo_debts');
    $PAGE->set_title($plugin_name);
    $PAGE->set_heading($plugin_name);
    $PAGE->set_url(new moodle_url($url));
    $PAGE->navbar->add(
        $plugin_name, new moodle_url($url)
    );

    if (has_capability('local/cdo_debts:retake_view', $context)) {
        $params['capability'] = 'local/cdo_debts:retake_view';
    } else {
        \core\notification::error('У вас нет прав просматривать страницу');
    }
    $PAGE->requires->js_call_amd(
        'local_cdo_debts/index-app-lazy',
        'init',
        [
            $params
        ]
    );
} catch (coding_exception|moodle_exception $e) {
    var_dump($e);
}

echo $OUTPUT->header();

$rendererVuetify = $PAGE->get_renderer('local_cdo_debts', 'vuetify');
try {
    echo $rendererVuetify->render(new vuetify());
} catch (coding_exception $e) {
}
echo $OUTPUT->footer();
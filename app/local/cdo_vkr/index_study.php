<?php

use local_cdo_vkr\output\vuetify\renderable as vuetify;

require(__DIR__ . "/../../config.php");
require_capability('local/cdo_vkr:view', context_system::instance());

global $PAGE, $OUTPUT, $CFG, $USER;

try {
    $plugin_name = get_string('pluginname', 'local_cdo_vkr');
    $context = context_system::instance();
    $PAGE->set_context($context);
    $PAGE->set_heading($plugin_name);
    $PAGE->set_title($plugin_name);
    $PAGE->set_url(new moodle_url('/local/cdo_vkr/index.php'));
    $PAGE->requires->css(new moodle_url("https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css"));
    $PAGE->requires->css(new moodle_url("https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900"));
    $PAGE->requires->css(new moodle_url("https://use.fontawesome.com/releases/v5.7.0/css/all.css"));

    $params = [
        'user_id' => $USER->id,
        'levelEduCantBeReviewed' => explode(',', $CFG->exclude_edu_level) ?? []
    ];
    if (has_capability('local/cdo_vrk:user_teacher', $context)) {
        $params['capability'] = 'local/cdo_vrk:user_teacher';
    } else {
        $params['capability'] = 'local/cdo_vrk:user_study';
    }
    $params['capability'] = 'local/cdo_vrk:user_study'; // TODO DEV
    $PAGE->requires->js_call_amd(
        'local_cdo_vkr/index-app-lazy',
        'init',
        [
            $params
        ]
    );

} catch (coding_exception|moodle_exception $e) {
    //TODO if error?
}


echo $OUTPUT->header();
$rendererVuetify = $PAGE->get_renderer('local_cdo_vkr', 'vuetify');
echo $rendererVuetify->render(new vuetify());
echo $OUTPUT->footer();


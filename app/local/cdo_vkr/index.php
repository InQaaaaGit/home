<?php

use local_cdo_vkr\output\vuetify\renderable as vuetify;

require(__DIR__ . "/../../config.php");
#require_capability('local/cdo_vkr:view', context_system::instance()); //TODO
require_login();
global $PAGE, $OUTPUT, $CFG, $USER;

try {
    $context = context_system::instance();
    $PAGE->set_context($context);
    $plugin_name = get_string('pluginname', 'local_cdo_vkr');
    $PAGE->set_title($plugin_name);
    $PAGE->set_heading($plugin_name);
    $PAGE->set_url(new moodle_url('/local/cdo_vkr/index.php'));
    #$PAGE->requires->css(new moodle_url($CFG->wwwroot . "/local/cdo_vkr/css/materialdesignicons.min.css"));
    $PAGE->requires->css(new moodle_url("https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css"));
    $PAGE->requires->css(new moodle_url("https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900"));
    #$PAGE->requires->css(new moodle_url("https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css"));
    $PAGE->requires->css(new moodle_url("https://use.fontawesome.com/releases/v5.7.0/css/all.css"));
    #$PAGE->requires->css(new moodle_url($CFG->wwwroot . "/local/cdo_vkr/css/css.css"));
    $params = [
        'user_id' => $USER->id,
        'levelEduCantBeReviewed' => explode(',', $CFG->exclude_edu_level) ?? []
    ];
    /* */
    $its_dev = false;
    if ($its_dev) {
        if (has_capability('local/cdo_vrk:user_teacher', $context)) {
            $params['capability'] = 'local/cdo_vrk:user_teacher';
        } else {
            $params['capability'] = 'local/cdo_vrk:user_study';
        }
    } else {
        require_once $CFG->dirroot . '/blocks/buttons/lib.php';
        $type = (new general_function)->getCurrentType();
        if ($type->type === 'teacher') {
            $params['capability'] = 'local/cdo_vrk:user_teacher';
        } else {
            $params['capability'] = 'local/cdo_vrk:user_study';
        }
    }


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


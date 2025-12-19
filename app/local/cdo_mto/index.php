<?php

require(__DIR__ . "/../../config.php");
require_login();
global $PAGE, $OUTPUT, $CFG, $USER;

try {
  $context = context_system::instance();
  $PAGE->set_context($context);
  $plugin_name = get_string('pluginname', 'local_cdo_mto');
  $url = new moodle_url($CFG->wwwroot . '/local/cdo_mto/index.php');
  $PAGE->set_title($plugin_name);
  $PAGE->set_heading($plugin_name);
  $PAGE->set_url($url);
  $PAGE->requires->css(new moodle_url("https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css"));
  $PAGE->requires->css(new moodle_url("https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900"));
  $PAGE->requires->css(new moodle_url("https://use.fontawesome.com/releases/v5.7.0/css/all.css"));

  $PAGE->navbar->add(
    $plugin_name, $url
  );

  $user_id = $USER->id;
  if (is_siteadmin()) {
    $user_id = '4117';
  }

  $PAGE->requires->js_call_amd(
    'local_cdo_mto/app-lazy',
    'init',
    [
      ['user_id' => $user_id]
    ]
  );

} catch (coding_exception|moodle_exception $e) {
  //TODO if error?
}

echo $OUTPUT->header();
echo '
<div id="main_app">
  <div class="spinner-border text-primary" role="status">
    <span class="sr-only"></span>
  </div>
</div>';
echo $OUTPUT->footer();


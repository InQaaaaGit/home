<?php
require_once(__DIR__ . "/../../config.php");
require_login();
$plugin_name = 'block_cdo_schedule';
$date_1c = required_param('date_1c', PARAM_TEXT);
$start_time = required_param('start_time', PARAM_TEXT);
$end_time = required_param('end_time', PARAM_TEXT);
$discipline = required_param('discipline', PARAM_TEXT);
$edu_plan = required_param('edu_plan', PARAM_TEXT);
$lesson_type = required_param('lesson_type', PARAM_TEXT);
$period_of_study = required_param('period_of_study', PARAM_TEXT);
$training_course = required_param('training_course', PARAM_TEXT);
$employee = required_param('employee', PARAM_TEXT);
$group = required_param('group', PARAM_TEXT);
global $PAGE, $OUTPUT;
$title = get_string('attendance', $plugin_name); // Add translations here if needed.
$url = new moodle_url('/blocks/cdo_schedule/attendance.php');
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
  //  $plugin_name."/app-lazy-attendance",
    $plugin_name."/attendance-app-lazy",
    'init',
    [
        [
            'date1c' => $date_1c,
            'discipline' => $discipline,
            'edu_plan' => $edu_plan,
            'lesson_type' => $lesson_type,
            'period_of_study' => $period_of_study,
            'group' => $group,
            'training_course' => $training_course,
            'employee' => $employee,
            'time_start' => $start_time,
            'time_end' => $end_time,
        ]
    ]
);
echo $OUTPUT->header();
echo
'<div id="main_app">
    <div class="spinner-border text-primary" role="status">
      <span class="sr-only"></span>
    </div>
    <app></app>
</div>';
echo $OUTPUT->footer();

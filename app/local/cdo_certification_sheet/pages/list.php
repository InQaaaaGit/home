<?php

use core\output\notification;
use local_cdo_certification_sheet\output\sheet_list\renderable as sheet_list;

require(__DIR__ . '/../../../config.php');

global $OUTPUT, $PAGE;

$usr = new moodle_url('/local/cdo_certification_sheet/pages/list.php');
$page_name = get_string('pluginname', 'local_cdo_certification_sheet');

$context = context_system::instance();

$PAGE->set_context(context_system::instance());

$PAGE->set_url($usr);
$PAGE->set_title($page_name);
$PAGE->set_heading($page_name);

if(!has_capability("local/cdo_certification_sheet:view", $context)) {
	$redirect = new moodle_url('/my');
	redirect(
		$redirect,
		get_string('access_denied', 'local_cdo_certification_sheet', $page_name),
		null, notification::NOTIFY_ERROR
	);
}

$render = $PAGE->get_renderer('local_cdo_certification_sheet', 'sheet_list');


echo $OUTPUT->header();
echo $render->render(new sheet_list());
echo $OUTPUT->footer();
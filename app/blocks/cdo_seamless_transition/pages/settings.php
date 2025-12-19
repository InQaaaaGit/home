<?php

use block_cdo_seamless_transition\forms\setting_form;
use block_cdo_seamless_transition\forms\validation_form;
use block_cdo_seamless_transition\models\setting_model;
use block_cdo_seamless_transition\settings\setting_seamless_transition;
use core\notification;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\tools\tool;

require_once("{$_SERVER['DOCUMENT_ROOT']}/config.php");

global $PAGE, $OUTPUT;

tool::access_admin();

$context = context_system::instance();
$PAGE->set_context($context);

$page_name = get_string('pluginname', 'block_cdo_seamless_transition');

$current_url = new moodle_url('/blocks/cdo_seamless_transition/pages/settings.php');

$PAGE->set_url($current_url);
$PAGE->set_title(get_string("setting_external_pages", "block_cdo_seamless_transition"));
$PAGE->set_heading(get_string("setting_external_pages", "block_cdo_seamless_transition"));

$setting_seamless_transition = new setting_seamless_transition();

tool::get_admin_default_navbar()
	->add($setting_seamless_transition->get_directory_name(),
		"/admin/category.php?category={$setting_seamless_transition->get_directory_code()}")
	->add($setting_seamless_transition->get_name(), $current_url);

$setting_model = new setting_model();

$form = new setting_form();

$form->set_data($setting_model->get_list_to_form());

$admin_url = new moodle_url("/admin/search.php#linkcdo_config");

if ($form->is_cancelled()) {
	redirect($admin_url);
} else if ($data = (array) $form->get_data()) {
	$valid_data = new validation_form($data);
	$valid_data->valid();
	$setting_data = $setting_model->set_data($valid_data->get_data());
	try {
		$setting_data->save();
		redirect(
			$current_url,
			get_string("save_success", "block_cdo_seamless_transition"),
			0,
			notification::SUCCESS
		);
	} catch (cdo_config_exception $e) {
		redirect($current_url, $e->getMessage(), 0, notification::ERROR);
	}
} else {
	echo $OUTPUT->header();
	$form->display();
	echo $OUTPUT->footer();
}
<?php

use local_cdo_certification_sheet\forms\setting_form;
use local_cdo_certification_sheet\models\setting_model;
use local_cdo_certification_sheet\settings\setting_certification_sheet;
use tool_cdo_config\tools\tool;

require_once("{$_SERVER['DOCUMENT_ROOT']}/config.php");

global $PAGE, $OUTPUT;

tool::access_admin();

$context = context_system::instance();
$PAGE->set_context($context);

$page_name = get_string('pluginname', 'local_cdo_certification_sheet');

$current_url = new moodle_url('/local/cdo_certification_sheet/pages/settings.php');

$PAGE->set_url($current_url);
$PAGE->set_title("set_title");
$PAGE->set_heading("set_heading");

$setting_certification_sheet = new setting_certification_sheet();

tool::get_admin_default_navbar()
	->add($setting_certification_sheet->get_directory_name(),
		"/admin/category.php?category={$setting_certification_sheet->get_directory_code()}")
	->add($setting_certification_sheet->get_name(), $current_url);

$form = new setting_form();

$form->set_data([
	'setting_form_code' => setting_model::get_code()
]);

$admin_url = new moodle_url("/admin/search.php");

if ($form->is_cancelled()) {
	redirect($admin_url);
} else if ($data = (array) $form->get_data()) {
	if (setting_model::save_code($data['setting_form_code'])) {
		redirect($current_url, "Данные успешно сохранены!");
	} else {
		throw new \tool_cdo_config\exceptions\cdo_config_exception("Ошибка при сохранение данных!", 2435457);
	}
} else {
	echo $OUTPUT->header();
	$form->display();
	echo $OUTPUT->footer();
}
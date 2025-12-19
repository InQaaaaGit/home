<?php

use tool_cdo_config\configs\main_config;
use tool_cdo_config\models\cdo_config;
use tool_cdo_config\tools\tool;

require_once(__DIR__."/../../../../../../config.php");
global $CFG, $PAGE, $OUTPUT, $USER;

tool::access_admin();

$context = context_system::instance();
$url = new moodle_url('/admin/tool/cdo_config/pages/settings/integrations/list.php');

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string("plugin_full_name", main_config::$component));
$PAGE->set_heading(get_string("settings_integrations_list_list_request", main_config::$component));

$single_url = '/admin/tool/cdo_config/pages/settings/integrations/single.php';

$content = new stdClass();
$content->url_create = new moodle_url($single_url, ['action' => 'create']);
$content->list = array_values(cdo_config::get_instance()->get_list());

foreach ($content->list as $item) {
	$item->url_delete = new moodle_url($single_url, ['action' => 'delete', 'id' => $item->id]);
	$item->url_edit = new moodle_url($single_url, ['action' => 'edit', 'id' => $item->id]);
}

$PAGE->requires->js_call_amd('tool_cdo_config/main', 'init');
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('tool_cdo_config/settings/integrations/list', $content);
echo $OUTPUT->footer();
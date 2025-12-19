<?php

use core\notification;
use tool_cdo_config\configs\main_config;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\forms\setting_integration_form;
use tool_cdo_config\models\cdo_config;
use tool_cdo_config\tools\tool;

require_once(__DIR__ . "/../../../../../../config.php");
global $CFG, $PAGE, $OUTPUT;

tool::access_admin();

$CREATE_ACTION = 'create';
$EDIT_ACTION = 'edit';
$DELETE_ACTION = 'delete';
$list_url = new moodle_url('/admin/tool/cdo_config/pages/settings/integrations/list.php');

$action = required_param('action', PARAM_TEXT);
$id = optional_param('id', '', PARAM_TEXT);

if ($action === $DELETE_ACTION) {
    $id = required_param('id', PARAM_TEXT);
    $cdo_config = cdo_config::get_instance()->delete($id);
    redirect(
        $list_url,
        get_string("settings_integrations_single_deleted", main_config::$component),
        0,
        notification::SUCCESS
    );
}

$context = context_system::instance();

$url_params = ['action' => $action];
if ($id !== '') {
    $url_params['id'] = $id;
}

$url = new moodle_url('/admin/tool/cdo_config/pages/settings/integrations/single.php', ['action' => $action]);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string("plugin_full_name", main_config::$component));

$cdo_config = cdo_config::get_instance();
$form = new setting_integration_form();

$heading = null;

if ($id !== "") {
    $detail = $cdo_config->get_detail($id);
    if (!$detail) {
        throw new cdo_config_exception(3001);
    }

    $_detail = [];

    $heading = get_string('settings_integrations_single_edit_request', main_config::$component, $detail->name);

    foreach ($detail as $key => $item) {
        if ($key !== 'id') {
            $key = setting_integration_form::$prefix . $key;
        }
        $_detail[$key] = $item;
    }

    $form->set_data($_detail);
}

$PAGE->set_heading($heading ?? get_string('settings_integrations_single_create_request', main_config::$component));
//TODO доделать navbar

if ($form->is_cancelled()) {
    redirect($list_url);
} else if ($data = (array)$form->get_data()) {
    $save_data = [];
    foreach ($data as $key => $item) {
        $key = str_replace(setting_integration_form::$prefix, "", $key);
        if ($key === 'method') {
            $item = $form->get_method_name($item);
        }
        if ($key === 'dto') {
            $item = str_replace('/', '\\', $item);
        }
        $save_data[$key] = $item;
    }

    $cdo_config->set_data((object)$save_data)->save();

    $message = get_string("settings_integrations_single_saved", main_config::$component);
    redirect($list_url, $message, 0, notification::SUCCESS);
} else {
    echo $OUTPUT->header();
    $form->display();
    echo $OUTPUT->footer();
}

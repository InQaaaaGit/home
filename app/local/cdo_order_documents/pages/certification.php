<?php

use core\notification;
use local_cdo_order_documents\forms\document_struct_form;
use local_cdo_order_documents\output\order_documents\renderable as order_documents;
use local_cdo_order_documents\services\main_service;
use local_cdo_order_documents\output\order_documents\renderable;
use tool_cdo_config\tools\dumper;
use local_cdo_order_documents\forms;

require_once(__DIR__ . "/../../config.php");

require_login();

global $PAGE, $OUTPUT, $USER;

$title = get_string('pluginname', 'local_cdo_order_documents');
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url('/local/cdo_order_documents/index.php');
$PAGE->set_heading($title);
$PAGE->set_title($title);
echo $OUTPUT->header();
/*$handler = new order_documents();
$list = $handler->get_types_references();*/


$trf = new forms\types_references_form(
    null, null, 'POST'
);
$trf->display();
$choose_document_type = optional_param('document_type', '', PARAM_TEXT);

if (!empty($choose_document_type) && $choose_document_type !== '000000000') {

    $dsf = new document_struct_form(
        null,
        ['document_type' => $choose_document_type],
        'POST',
        ''
    );

    if ($dsf->is_cancelled()) {
        $dsf->display();
    } else if ($fromform = $dsf->get_data()) {
        $grade_book = '';
        $fields_array = [];
        foreach ($fromform as $key => $val) {
            if ($key === 'submitbutton' || $key === 'document_type') {
                continue;
            }
            $fields_array_element['name'] = $key;
            $fields_array_element['value'] = $val;
            $fields_array[] = $fields_array_element;
            if ($fields_array_element['name'] === 'ЗачетнаяКнига') {
                $grade_book = $fields_array_element['value'];
            }
        }
        $result = main_service::send_document(
            $USER->id,
            # 34143, //TODO
            $fromform->document_type,
            $fields_array,
            $grade_book
        );
        if (key_exists('error_message',$result)) {
            notification::error($result['error_message']);
        }
        elseif (!$result['success']) {
            notification::warning($result['message']);
        } else {
            notification::success($result['message']);
        }
    } else {
        $dsf->display();
    }


} else {
    notification::warning('Вид справки не выбран');
}
$new = new renderable();
$list = $new->get_certificates();

if (!empty($list)) {
    if (array_key_exists('error_message', $list)) {
        notification::warning($list['error_message']);
    } else
        echo $OUTPUT->render_from_template('local_cdo_order_documents/certificates_table', ['data' => $list]);
}

echo $OUTPUT->footer();

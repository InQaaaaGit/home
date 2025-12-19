<?php
require(__DIR__ . '/../../config.php');
require('lib.php');
require_once('form_add.php');

global $USER, $CFG, $OUTPUT, $PAGE, $DB;
if (!is_siteadmin()) {
    redirect(new moodle_url("/"));
}
$title = get_string('pluginname', 'block_slider');
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_url('/blocks/slider/admin.php');
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add($title, '/blocks/slider/admin.php');
$PAGE->navbar->add("Редактирование", '/blocks/slider/edit.php');

$mform = new simplehtml_form();
$id = optional_param("id", 0, PARAM_INT);
//var_dump($DB->get_records("files" )); die();
if ($mform->is_cancelled()) {

} else if ($fromform = $mform->get_data()) {
  #  var_dump(); die();
    $update = $mform->get_data();
    $update->id = $id;

    #var_dump($update); die();
    if ($mform->get_file_content('slide_file')) {
        $storedfile = $mform->save_stored_file(
            'slide_file',
            $PAGE->context->id,
            "block_slider",
            "slide",
            $id
        );
        $update->file_id = $storedfile->get_id();;
    }
    update_slide($update);
    redirect($PAGE->url);

} else {
    $data = get_slide_by_id($id);
    $mform->set_data($data);
}

echo $OUTPUT->header();


$mform->display();
echo $OUTPUT->footer();

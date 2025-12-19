<?php
require(__DIR__ . '/../../config.php');
require('lib.php');
require_once('form_add.php');

global $USER, $CFG, $OUTPUT, $PAGE;
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

$mform = new simplehtml_form();
$deletion = optional_param("delete", 0, PARAM_INT);

if (!empty($deletion)) {
    delete_slide($deletion);
}
if ($mform->is_cancelled()) {

} else if ($fromform = $mform->get_data()) {

    $itemid = create_slide($fromform);
    $storedfile = $mform->save_stored_file(
        'slide_file',
        $PAGE->context->id,
        "block_slider",
        "slide",
        $itemid
    );


    $clone = $fromform;
    $clone->file_id = $storedfile->get_id();
    $clone->id = $itemid;
    update_slide($clone);

    redirect($PAGE->url);
    $mform->set_data(["slide_text"=>""]);
} else {
    $mform->set_data([]);
}

echo $OUTPUT->header();
$table = new html_table();
$table->head = [
    "#",
    get_string('slider_slide_head', 'block_slider'),
    get_string('slider_slide_text', 'block_slider'),
    get_string('slider_slide_section', 'block_slider'),
    get_string('slider_slide_text_company', 'block_slider'),
    get_string('slider_slide_email', 'block_slider'),
    get_string('slider_slide_telephone', 'block_slider'),
    "Действия"];

$table->data = convert_slider_data_to_table();

echo html_writer::table($table);

$mform->display();
echo $OUTPUT->footer();

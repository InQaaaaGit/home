<?php
require(__DIR__ . '/../../config.php');
require('lib.php');
require_once('form_add_img.php');

global $USER, $CFG, $OUTPUT, $PAGE;
if (!is_siteadmin()) {
    redirect(new moodle_url("/"));
}
$cat_id = optional_param("cat_id", "",PARAM_INT);

$ccc = context_coursecat::instance($cat_id);
$context = context_system::instance();
$title = get_string('cat_add_img', 'block_slider');
require_login();
$PAGE->set_context($ccc);
$PAGE->set_title($title);
$PAGE->set_url('/blocks/slider/admin.php');
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add($title, '/blocks/slider/admin.php');


$mform = new simplehtml_form();

if ($mform->is_cancelled()) {

} else if ($data = $mform->get_data()) {
    #$ccc = context_coursecat::instance($cat_id);
    file_save_draft_area_files($data->attachments, $ccc->id, 'block_slider', 'attachment',
        0, array('subdirs' => 0, 'maxbytes' => 200, 'maxfiles' => 50));
    redirect('/blocks/slider/list_categories.php');
} else {
    $mform->set_data(['cat_id'=>$cat_id]);
}

echo $OUTPUT->header();

$mform->display();
echo $OUTPUT->footer();

<?php

use local_cdo_ag_tools\forms\upload_form;
use local_cdo_ag_tools\helpers\upload;
use PhpOffice\PhpSpreadsheet\IOFactory;

require_once(__DIR__ . "/../../config.php");
global $CFG, $PAGE, $OUTPUT;
require_once("$CFG->libdir/formslib.php");
require_login();
$plugin = 'local_cdo_ag_tools';
$title = get_string('pluginname', $plugin);
$url = new moodle_url('/local/cdo_ag_tools/upload.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url($url);

echo $OUTPUT->header();

$mform = new upload_form();

if ($mform->is_cancelled()) {

} else if ($fromform = $mform->get_data()) {
    $name = $mform->get_new_filename('grade_report');
    $tempFile = $CFG->tempdir . '/' . $name;
    $success = $mform->save_file('grade_report', $tempFile, true);
    if ($success) {
        // TODO use fabric for future
        upload::parse_and_save_grades_from_uploaded_data($tempFile);
    }
    unlink($tempFile);

} else {
    $mform->display();
}
echo $OUTPUT->footer();
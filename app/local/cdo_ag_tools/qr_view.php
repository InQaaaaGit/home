<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');

use local_cdo_ag_tools\controllers\repository_works;
use local_cdo_ag_tools\qr\generate_pdf;
use local_cdo_ag_tools\qr\generate_qr;
use local_cdo_ag_tools\qr\render;
use local_cdo_ag_tools\task\create_archive;

$PAGE->set_url('/local/cdo_ag_tools/qr_view.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('qrcodegenerator', 'local_cdo_ag_tools')); // Set a descriptive title
$PAGE->set_heading(get_string('qrcode', 'local_cdo_ag_tools')); // Set a heading

require_login();

global $USER;
$task = new create_archive();
$task->set_custom_data(['userid' => $USER->id]);

$task->set_component('local_cdo_ag_tools');
$task->execute();
$s = new repository_works(
    new generate_pdf(
        new generate_qr(
            new render()
        )
    ),
    $USER->id // Pass the user ID
);
$s->generate_output_zip_with_works();
//\core\task\manager::queue_adhoc_task($task, true);
//redirect('/my', get_string('yourpersonalqrcode', 'local_cdo_ag_tools'));
echo $OUTPUT->header();

echo '<h2>'.get_string('yourpersonalqrcode', 'local_cdo_ag_tools').'</h2>'; // User-friendly heading
echo '<p>'.get_string('uniqueqrcode', 'local_cdo_ag_tools').'</p>'; // Descriptive text

echo $OUTPUT->footer();

<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_cdo_ag_tools\strategies\direct_send_grade_strategy;
use local_cdo_ag_tools\helpers\grade_data_helper;
use grade_item;

admin_externalpage_setup('local_cdo_ag_tools_test_send_grade');

echo $OUTPUT->header();

$PAGE->set_title(get_string('test_send_grade_page_title', 'local_cdo_ag_tools'));
echo $OUTPUT->heading(get_string('test_send_grade_page_title', 'local_cdo_ag_tools'));

echo '<form method="post">';
echo '<button type="submit" name="send_test" class="btn btn-primary">' . get_string('send_test_grade_button', 'local_cdo_ag_tools') . '</button>';
echo '</form>';

if (isset($_POST['send_test'])) {
    echo $OUTPUT->box_start();
    debugging("Starting test grade send...", DEBUG_DEVELOPER);

    $itemid = 10101;
    $userid = 2785;
    $grade_value = 1.0;
    $itemtype = 'course';

    try {
        global $DB;
        $grade_item = grade_item::fetch(['id' => $itemid, 'itemtype' => $itemtype]);
        
        if (!$grade_item) {
            throw new Exception("Grade item with ID {$itemid} not found.");
        }

        $course_id = $grade_item->courseid;
        $section_id = grade_data_helper::get_section_id_for_grade_item($grade_item, $course_id);

        $grade_info = [
            "course_id" => (string)$course_id,
            "user_id" => (string)$userid,
            "grade" => $grade_value,
            "section_id" => $section_id,
            "item_type" => $itemtype
        ];

        debugging("Test grade info: " . json_encode($grade_info), DEBUG_DEVELOPER);

        $strategy = new direct_send_grade_strategy();
        $success = $strategy->handle_grade($grade_info);

        if ($success) {
            echo $OUTPUT->notification(get_string('test_send_success', 'local_cdo_ag_tools'), 'notifysuccess');
            debugging("Test grade sent successfully.", DEBUG_DEVELOPER);
        } else {
            echo $OUTPUT->notification(get_string('test_send_failed', 'local_cdo_ag_tools'), 'notifyproblem');
            debugging("Test grade sending failed.", DEBUG_DEVELOPER);
        }

    } catch (Exception $e) {
        echo $OUTPUT->notification(get_string('test_send_error', 'local_cdo_ag_tools') . ': ' . $e->getMessage(), 'notifyproblem');
        debugging("Error during test grade send: " . $e->getMessage(), DEBUG_DEVELOPER);
    }
    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer(); 
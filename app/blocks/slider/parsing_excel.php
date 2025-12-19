<?php

require(__DIR__ . '/../../config.php');
global $CFG;
require('lib.php');
require_once('form_parsing_excel.php');
require_once($CFG->libdir . "/csvlib.class.php");
require_once($CFG->dirroot . "/enrol/manual/externallib.php");
global $USER, $CFG, $OUTPUT, $PAGE;
if (!is_siteadmin()) {
    redirect(new moodle_url("/"));
}
#$title = get_string('pluginname', 'block_slider');
$title = 'Форма для загрузки';
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_url('/blocks/slider/admin.php');
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add($title, '/blocks/slider/parsing_excel.php');

$mform = new parsing_form();

$PAGE->requires->js_call_amd('block_slider/parsing_excel', 'init',
    [1]
);
$table = "";
if ($mform->is_cancelled()) {

} else if ($fromform = $mform->get_data()) {
    $temp = $mform->save_temp_file("parsingFile");
    if (!$temp || !isset($fromform->modList)) {
        \core\notification::error("файл не выбран или модуль");
    } else {
        $course = $fromform->courseList;
        $module = $fromform->modList;
        $fmi = get_fast_modinfo($course);
        $courseName = ($fmi->get_course()->fullname);
        $modName = $fmi->get_cm($module)->name;

        $csvFile = file($temp);
        $rows = array_map(function ($line) {
            return str_getcsv($line, ";");
        }, $csvFile);
        array_shift($rows); //shift first element because this is header
        foreach ($rows as $row) {
            $userData = get_complete_user_data("username", $row[1]);
            if ($userData) {
                $userid = $userData->id;
                try {
                     enrol_manual_external::enrol_users([
                        [
                            "roleid" => 5, //student
                            "userid" => $userid,
                            "courseid" => $course
                        ]
                    ]);
                    $result = core_completion_external::override_activity_completion_status($userid, $module, 1);
                    $userItemData = [$userData->email, $modName, $courseName, !!$result['state'] ? "Успешно" : "Ошибка"];
                } catch (moodle_exception $e) {
                    $userItemData = [$userData->email, $modName, $courseName, $e->debuginfo];
                }

            }
        }

        $table = new html_table();
        $table->id = "logs";
        $table->attributes = ["class" => "table table-striped"];
        $table->data[] = $userItemData;
        $table->head = ["email", "Модуль", "Курс/Дисциплина", "Результат"];
        $table = html_writer::table($table);
    }

} else {

}


echo $OUTPUT->header();
$mform->display();
echo $table;
echo $OUTPUT->footer();

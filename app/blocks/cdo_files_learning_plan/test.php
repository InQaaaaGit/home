<?php

require_once __DIR__ . '/../../config.php';
global $CFG, $USER, $OUTPUT, $DB, $PAGE;

require_login();
if ($USER->id <= 1) {
    echo "Зайдите в свой личный кабинет!";
    die;
}

// Проверка прав доступа
require_capability('block/files_learning_plan:view', context_system::instance());

$title = "Прикрепление файлов к образовательной программе";
$url = '/blocks/files_learning_plan/index.php';

$PAGE->set_context(context_system::instance());

$PAGE->set_title($title);
$PAGE->set_url($url);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('base');
$PAGE->navbar->add($title, $url);

$PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/files_learning_plan/style/style2.css?1'));

echo $OUTPUT->header();

echo '<div id="app-files-learning-plan"></div>
     <script src="/blocks/files_learning_plan/amd/build/files-program-lazy.min.js?16"></script>';

echo $OUTPUT->footer();

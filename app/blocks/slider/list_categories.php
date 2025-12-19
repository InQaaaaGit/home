<?php
require_once(__DIR__ . '/../../config.php');


require_login();
global $USER, $CFG, $OUTPUT, $PAGE;
require_once($CFG->dirroot . '/blocks/slider/lib.php');
if (!is_siteadmin()) {
    redirect(new moodle_url("/"));
}
$context = context_system::instance();
$title = get_string('cat_add_img', 'block_slider');

$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_url('/blocks/slider/list_categories.php');
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add($title, '/blocks/slider/list_categories.php');

echo $OUTPUT->header();

$table = new html_table();
$table->head = [
    "#",
    get_string('category', 'moodle'),
    "Действия"
];

//получаем все айди дочерних категорий - сразу всего списка не дают.
$data = get_categories_by_parent($CFG->name_category_open_course_id);
$table->data = $data;
echo html_writer::tag("h2", "Категории открытых курсов.");
echo html_writer::table($table);
$data = get_categories_by_parent($CFG->all_courses_link);
$table->data = $data;
echo html_writer::tag("h2", "Категории профессиональных курсов.");
echo html_writer::table($table);
echo $OUTPUT->footer();

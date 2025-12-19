<?php

require_once(__DIR__ . "/../../../config.php");
defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE, $OUTPUT, $DB;

use local_cdo_unti2035bas\grades\handler;

require_login();
require_capability('moodle/site:config', \context_system::instance());

// ------------------- Параметры и настройка страницы -------------------
$flowId = required_param('flow_id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

// Определяем, какие user_ids использовать, в зависимости от нажатой кнопки
if (isset($_POST['send_all'])) {
    $userIds = optional_param_array('all_user_ids', [], PARAM_INT);
} else {
    $userIds = optional_param_array('user_ids', [], PARAM_INT);
}

$context = \context_system::instance();

$PAGE->set_url(new \moodle_url('/local/cdo_unti2035bas/pages/send_grades.php', ['flow_id' => $flowId]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

// --- Настройка "хлебных крошек" ---
$PAGE->navbar->add(get_string('pluginname', 'local_cdo_unti2035bas'));
$PAGE->navbar->add(get_string('streamsdetails', 'local_cdo_unti2035bas'), new \moodle_url('/local/cdo_unti2035bas/streams.php'));
$PAGE->navbar->add(get_string('sendgradesforstream', 'local_cdo_unti2035bas'));

$PAGE->set_title(get_string('sendgradesforstream', 'local_cdo_unti2035bas'));
$PAGE->set_heading($PAGE->title);
$PAGE->requires->css('/local/cdo_unti2035bas/styles.css');
$PAGE->requires->js_init_call('M.local_cdo_unti2035bas.init_select_all');

// ------------------- Получение данных -------------------
$stream = $DB->get_record('cdo_unti2035bas_stream', ['untiflowid' => $flowId], '*', MUST_EXIST);
$course = $DB->get_record('course', ['id' => $stream->courseid], '*', MUST_EXIST);
$group = $DB->get_record('groups', ['id' => $stream->groupid], '*', MUST_EXIST);
$users = groups_get_members($stream->groupid, 'u.id, u.firstname, u.lastname, u.email');
$results = null;

// ------------------- Логика отправки -------------------
if ($confirm && !empty($userIds) && !empty($stream) && !empty($course)) {
    try {
        $handler = new handler();
        $results = $handler->get_grades_for_users($course->id, $userIds, $flowId);
    } catch (\Exception $e) {
        $results = ['errors' => 1, 'details' => ['errors' => [['error_message' => $e->getMessage()]]]];
        debugging($e);
    }
}

// ------------------- Отображение страницы -------------------
echo $OUTPUT->header();
echo \html_writer::start_div('send-grades-container');
echo $OUTPUT->heading($PAGE->title);

// --- Отображение результатов отправки (если они есть) ---
if ($results) {
    $sent = $results['sent'] ?? 0;
    $skipped = $results['skipped'] ?? 0;
    $errors = $results['errors'] ?? 0;
    $total = $results['total'] ?? 0;

    $message_parts = [];
    $message_parts[] = get_string('grade_sent_count', 'local_cdo_unti2035bas', $sent);
    if ($skipped > 0) $message_parts[] = get_string('grade_skipped_count', 'local_cdo_unti2035bas', $skipped);
    if ($errors > 0) $message_parts[] = get_string('grade_errors_count', 'local_cdo_unti2035bas', $errors);
    $message_parts[] = get_string('grade_total_processed', 'local_cdo_unti2035bas', $total);
    
    echo $OUTPUT->notification(implode(', ', $message_parts), ($errors > 0) ? 'warning' : 'success');
}

// --- Информационный блок о потоке (в стиле карточек) ---
echo \html_writer::start_div('flow-info-section');
echo $OUTPUT->heading(get_string('streaminfo', 'local_cdo_unti2035bas'), 3);
echo \html_writer::start_div('flow-info-grid');

echo \html_writer::div(
    \html_writer::div(get_string('flowid', 'local_cdo_unti2035bas'), 'info-label') .
    \html_writer::div($flowId, 'info-value'),
    'flow-info-item'
);
echo \html_writer::div(
    \html_writer::div(get_string('course'), 'info-label') .
    \html_writer::div($course->fullname, 'info-value'),
    'flow-info-item'
);
echo \html_writer::div(
    \html_writer::div(get_string('group'), 'info-label') .
    \html_writer::div($group->name, 'info-value'),
    'flow-info-item'
);

echo \html_writer::end_div(); // flow-info-grid
echo \html_writer::end_div(); // flow-info-section


// --- Таблица со студентами для выбора ---
if (!empty($users)) {
    echo \html_writer::empty_tag('hr');
    echo $OUTPUT->heading(get_string('selectuserstosendgrades', 'local_cdo_unti2035bas'), 3);
    
    echo \html_writer::start_tag('form', ['action' => new \moodle_url('send_grades.php'), 'method' => 'post']);
    echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'flow_id', 'value' => $flowId]);
    echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'confirm', 'value' => '1']);

    $table = new \html_table();
    $table->head = [
        \html_writer::empty_tag('input', ['type' => 'checkbox', 'id' => 'select-all-users']),
        get_string('fullname'),
        get_string('email')
    ];
    $table->attributes['class'] = 'generaltable flexible';

    foreach ($users as $user) {
        $checkbox = \html_writer::empty_tag('input', ['type' => 'checkbox', 'name' => 'user_ids[]', 'value' => $user->id, 'class' => 'user-checkbox']);
        $table->data[] = [$checkbox, fullname($user), $user->email];
    }
    echo \html_writer::table($table);
    
    // Добавляем скрытые поля для всех user_id
    if (!empty($users)) {
        foreach ($users as $user) {
            echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'all_user_ids[]', 'value' => $user->id]);
        }
    }

    echo \html_writer::start_div('send-buttons');
    // Кнопка для отправки ВЫБРАННЫХ
    echo \html_writer::empty_tag('input', ['type' => 'submit', 'name' => 'send_selected', 'class' => 'btn btn-primary', 'value' => get_string('sendselected', 'local_cdo_unti2035bas')]);
    
    // Кнопка для отправки ВСЕХ
    echo \html_writer::empty_tag('input', ['type' => 'submit', 'name' => 'send_all', 'class' => 'btn btn-success ml-2', 'value' => get_string('sendforallusers', 'local_cdo_unti2035bas'), 'onclick' => "return confirm('" . get_string('confirmsendforallusers', 'local_cdo_unti2035bas') . "')"]);
    
    echo \html_writer::end_div();
    echo \html_writer::end_tag('form');

} else {
    echo $OUTPUT->notification(get_string('nousersfound', 'local_cdo_unti2035bas'));
}

echo \html_writer::end_div();
echo $OUTPUT->footer();

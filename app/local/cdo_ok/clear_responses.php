<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Страница очистки ответов на анкеты.
 *
 * @package    local_cdo_ok
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_cdo_ok_clear_responses');

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$confirm = optional_param('confirm', 0, PARAM_INT);
$sesskey = optional_param('sesskey', '', PARAM_RAW);

$PAGE->set_url(new moodle_url('/local/cdo_ok/clear_responses.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('clear_responses_title', 'local_cdo_ok'));
$PAGE->set_heading(get_string('clear_responses_title', 'local_cdo_ok'));

// Обработка подтверждения удаления.
if ($confirm && confirm_sesskey($sesskey)) {
    try {
        $transaction = $DB->start_delegated_transaction();
        
        // Подсчитываем количество записей для отчета.
        $answersCount = $DB->count_records('local_cdo_ok_answer');
        $confirmAnswersCount = $DB->count_records('local_cdo_ok_confirm_answers');
        $totalCount = $answersCount + $confirmAnswersCount;
        
        // Удаляем все ответы.
        $DB->delete_records('local_cdo_ok_answer');
        $DB->delete_records('local_cdo_ok_confirm_answers');
        
        $transaction->allow_commit();
        
        // Выводим сообщение об успехе.
        redirect(
            new moodle_url('/local/cdo_ok/clear_responses.php'),
            get_string('clear_responses_success', 'local_cdo_ok', $totalCount),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } catch (Exception $e) {
        if (isset($transaction)) {
            $transaction->rollback($e);
        }
        
        redirect(
            new moodle_url('/local/cdo_ok/clear_responses.php'),
            get_string('clear_responses_error', 'local_cdo_ok', $e->getMessage()),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('clear_responses_title', 'local_cdo_ok'));

// Получаем статистику по ответам.
$answersCount = $DB->count_records('local_cdo_ok_answer');
$confirmAnswersCount = $DB->count_records('local_cdo_ok_confirm_answers');
$totalCount = $answersCount + $confirmAnswersCount;

// Выводим предупреждение.
echo html_writer::div(
    get_string('clear_responses_warning', 'local_cdo_ok'),
    'alert alert-danger',
    ['role' => 'alert']
);

// Выводим статистику.
echo html_writer::start_div('alert alert-info', ['role' => 'alert']);
echo html_writer::tag('h4', get_string('statistics', 'core'), ['class' => 'alert-heading']);
echo html_writer::tag('p', get_string('report:quantity_users', 'local_cdo_ok') . ': ' . 
    $DB->count_records_sql('SELECT COUNT(DISTINCT user_id) FROM {local_cdo_ok_answer}'));
echo html_writer::tag('p', 'Записей в таблице ответов: ' . $answersCount);
echo html_writer::tag('p', 'Записей в таблице подтверждений: ' . $confirmAnswersCount);
echo html_writer::tag('p', html_writer::tag('strong', 'Всего записей: ' . $totalCount));
echo html_writer::end_div();

// Форма подтверждения.
$confirmUrl = new moodle_url(
    '/local/cdo_ok/clear_responses.php',
    [
        'confirm' => 1,
        'sesskey' => sesskey(),
    ]
);

echo html_writer::start_div('mt-3');
echo html_writer::tag(
    'p',
    get_string('clear_responses_confirm', 'local_cdo_ok'),
    ['class' => 'font-weight-bold']
);

echo html_writer::start_div('btn-group', ['role' => 'group']);

// Кнопка подтверждения.
echo html_writer::link(
    $confirmUrl,
    get_string('clear_responses_button', 'local_cdo_ok'),
    [
        'class' => 'btn btn-danger',
        'onclick' => 'return confirm("' . 
            addslashes_js(get_string('clear_responses_confirm', 'local_cdo_ok')) . 
            '");',
    ]
);

// Кнопка отмены.
echo html_writer::link(
    new moodle_url('/admin/settings.php', ['section' => 'local_cdo_ok']),
    get_string('clear_responses_cancel', 'local_cdo_ok'),
    ['class' => 'btn btn-secondary ml-2']
);

echo html_writer::end_div();
echo html_writer::end_div();

echo $OUTPUT->footer();


<?php

require_once(__DIR__ . "/../../../config.php");
defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE, $OUTPUT, $DB;

use context_system;
use moodle_url;

// Получаем обязательный параметр flow_id
$flowId = required_param('flow_id', PARAM_INT);

$context = context_system::instance();
require_login();
require_capability('moodle/site:config', $context);

// Настройка страницы
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/pages/video_statement_test.php', ['flow_id' => $flowId]));
$title = 'Video Statements Test: ' . $flowId;
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();

echo html_writer::tag('h2', 'Тестовая страница Video Statements');
echo html_writer::tag('p', 'Flow ID: ' . $flowId);

// Проверим базовое подключение к базе данных
try {
    $streamCount = $DB->count_records('cdo_unti2035bas_stream');
    echo html_writer::tag('p', 'Общее количество потоков в БД: ' . $streamCount);
    
    // Попробуем найти поток напрямую через DB
    $stream = $DB->get_record('cdo_unti2035bas_stream', ['untiflowid' => $flowId]);
    if ($stream) {
        echo html_writer::tag('p', 'Поток найден! Course ID: ' . $stream->courseid . ', Group ID: ' . $stream->groupid, ['style' => 'color: green;']);
    } else {
        echo html_writer::tag('p', 'Поток с Flow ID ' . $flowId . ' не найден.', ['style' => 'color: red;']);
        
        // Покажем все доступные flow_id для отладки
        $flowIds = $DB->get_fieldset_select('cdo_unti2035bas_stream', 'untiflowid', 'untiflowid IS NOT NULL');
        if (!empty($flowIds)) {
            echo html_writer::tag('p', 'Доступные Flow IDs: ' . implode(', ', array_unique($flowIds)));
        } else {
            echo html_writer::tag('p', 'В таблице нет потоков с Flow ID.');
        }
    }
} catch (Exception $e) {
    echo html_writer::tag('p', 'Ошибка базы данных: ' . $e->getMessage(), ['style' => 'color: red;']);
}

echo html_writer::link(
    new moodle_url('/local/cdo_unti2035bas/streams.php'),
    'Назад к потокам',
    ['class' => 'btn btn-secondary', 'style' => 'margin-top: 20px;']
);

echo $OUTPUT->footer(); 
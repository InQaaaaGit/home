<?php
namespace local_cdo_unti2035bas;

use local_cdo_unti2035bas\video_progress\handler;
use stdClass;

/**
 * Скрипт для отправки xAPI statements о просмотре видео
 * 
 * Использование:
 * - Отправить все видео: /local/cdo_unti2035bas/xapi_send_video_statements.php
 * - Отправить для конкретного потока: /local/cdo_unti2035bas/xapi_send_video_statements.php?flow_id=123
 * 
 * @var stdClass $CFG
 */

require_once('../../config.php');
require_once("{$CFG->libdir}/adminlib.php");

admin_externalpage_setup('testxapiconf');

// Получаем flow_id из параметров, если передан
$flow_id = optional_param('flow_id', null, PARAM_INT);

// Валидация flow_id - проверяем, что поток существует
if ($flow_id) {
    global $DB;
    $stream_exists = $DB->record_exists('cdo_unti2035bas_stream', ['untiflowid' => $flow_id]);
    if (!$stream_exists) {
        $url = get_local_referer(false) ?: ($CFG->wwwroot . '/');
        redirect(new \moodle_url($url), "Поток с ID {$flow_id} не найден в системе", null, \core\output\notification::NOTIFY_ERROR);
    }
}

$handler = new handler();
$results = $handler->send_video_progress_statements($flow_id);

// Проверяем, есть ли данные для отправки
if ($results['total'] === 0) {
    if ($flow_id) {
        $result_message = "Нет данных для отправки для потока ID: {$flow_id}";
    } else {
        $result_message = get_string('xapi_no_data_to_send', 'local_cdo_unti2035bas');
    }
} else {
    // Формируем детальное сообщение о результатах
    $message_parts = [];
    
    if ($flow_id) {
        $message_parts[] = "Поток ID: {$flow_id}";
    }
    
    $message_parts[] = get_string('xapi_sent_count', 'local_cdo_unti2035bas', $results['sent']);

    if ($results['skipped'] > 0) {
        $message_parts[] = get_string('xapi_skipped_count', 'local_cdo_unti2035bas', $results['skipped']);
    }

    if ($results['errors'] > 0) {
        $message_parts[] = get_string('xapi_errors_count', 'local_cdo_unti2035bas', $results['errors']);
    }

    $message_parts[] = get_string('xapi_total_processed', 'local_cdo_unti2035bas', $results['total']);

    $result_message = implode(', ', $message_parts);
}

$url = get_local_referer(false) ?: ($CFG->wwwroot . '/');
redirect(new \moodle_url($url), $result_message);

<?php
namespace local_cdo_unti2035bas;

use context_system;
use html_writer;
use local_cdo_unti2035bas\form\grade_statements_form;
use local_cdo_unti2035bas\grades\handler;
use moodle_url;
use stdClass;

/**
 * @var stdClass $CFG
 */

require_once('../../config.php');
require_once("{$CFG->libdir}/adminlib.php");

// Проверка прав доступа
admin_externalpage_setup('testxapiconf');
require_capability('moodle/site:config', context_system::instance());

$context = context_system::instance();

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/grade_statements.php'));
$title = get_string('send_grade_statements_head', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Добавляем CSS для красивого отображения
$PAGE->requires->css(new moodle_url('/local/cdo_unti2035bas/styles.css'));

// Создаем форму
$form = new grade_statements_form();

$message = '';
$message_type = '';
$detailed_results = null;

// Обработка отправки формы
if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php', ['section' => 'local_cdo_unti2035bas']));
} else if ($data = $form->get_data()) {
    if ($data->action === 'send' && !empty($data->course_id)) {
        try {
            $handler = new handler();
            $results = $handler->get_grades($data->course_id);
            
            // Проверяем результаты
            if (!$results || (isset($results['total']) && $results['total'] === 0)) {
                $message = get_string('grade_no_data_to_send', 'local_cdo_unti2035bas');
                $message_type = \core\output\notification::NOTIFY_INFO;
            } else if (is_array($results) && isset($results['total'])) {
                // Сохраняем детальные результаты для отображения
                $detailed_results = $results;
                
                // Формируем краткое сообщение о результатах
                $message_parts = [];
                $message_parts[] = get_string('grade_sent_count', 'local_cdo_unti2035bas', $results['sent'] ?? 0);

                if (isset($results['skipped']) && $results['skipped'] > 0) {
                    $message_parts[] = get_string('grade_skipped_count', 'local_cdo_unti2035bas', $results['skipped']);
                }

                if (isset($results['errors']) && $results['errors'] > 0) {
                    $message_parts[] = get_string('grade_errors_count', 'local_cdo_unti2035bas', $results['errors']);
                }

                $message_parts[] = get_string('grade_total_processed', 'local_cdo_unti2035bas', $results['total']);

                $message = implode(', ', $message_parts);
                $message_type = ($results['errors'] > 0) ? \core\output\notification::NOTIFY_WARNING : \core\output\notification::NOTIFY_SUCCESS;
            } else {
                $message = get_string('send_grade_statements_result', 'local_cdo_unti2035bas', 'Выполнено');
                $message_type = \core\output\notification::NOTIFY_SUCCESS;
            }
        } catch (\Exception $e) {
            $message = 'Ошибка при отправке: ' . $e->getMessage();
            $message_type = \core\output\notification::NOTIFY_ERROR;
        }
    }
}

/**
 * Функция для отображения детальных результатов
 */
function display_detailed_results($results) {
    global $OUTPUT;
    
    if (!isset($results['details'])) {
        return '';
    }
    
    $output = '';
    $details = $results['details'];
    
    // Успешные отправки
    if (!empty($details['sent'])) {
        $output .= $OUTPUT->heading('✅ Успешно отправлено (' . count($details['sent']) . ')', 4);
        $output .= $OUTPUT->box_start('generalbox success-box');
        
        $table = new \html_table();
        $table->head = ['Пользователь', 'Email', 'Элемент оценки', 'Оценка', 'Время'];
        $table->attributes['class'] = 'table table-striped';
        
        foreach ($details['sent'] as $record) {
            $table->data[] = [
                $record['user_name'],
                $record['user_email'],
                $record['item_name'],
                $record['grade_value'] . '/' . $record['grade_max'],
                userdate($record['timestamp'])
            ];
        }
        
        $output .= html_writer::table($table);
        $output .= $OUTPUT->box_end();
    }
    
    // Ошибки - группируем по типам
    if (!empty($details['errors'])) {
        $output .= $OUTPUT->heading('❌ Ошибки (' . count($details['errors']) . ')', 4);
        
        // Группируем ошибки по типам
        $errors_by_type = [];
        foreach ($details['errors'] as $error) {
            $type = $error['error_type'] ?? 'unknown_error';
            $errors_by_type[$type][] = $error;
        }
        
        // Отображаем каждый тип ошибок
        foreach ($errors_by_type as $error_type => $error_records) {
            $type_name = get_error_type_name($error_type);
            $output .= $OUTPUT->heading($type_name . ' (' . count($error_records) . ')', 5);
            $output .= $OUTPUT->box_start('generalbox error-box');
            
            $table = new \html_table();
            $table->head = ['Пользователь', 'Email', 'Элемент оценки', 'Оценка', 'Ошибка', 'Время'];
            $table->attributes['class'] = 'table table-striped table-sm';
            
            foreach ($error_records as $record) {
                $row = [
                    $record['user_name'],
                    $record['user_email'],
                    $record['item_name'],
                    $record['grade_value'] . '/' . $record['grade_max'],
                    html_writer::tag('small', $record['error_message'], ['class' => 'text-danger']),
                    userdate($record['timestamp'])
                ];
                $table->data[] = $row;
            }
            
            $output .= html_writer::table($table);
            $output .= $OUTPUT->box_end();
        }
        
        // Кнопка для скачивания CSV с ошибками
        $output .= $OUTPUT->single_button(
            new moodle_url($GLOBALS['PAGE']->url, ['download_errors' => 1, 'course_id' => $_POST['course_id'] ?? 0]),
            '📥 Скачать отчет об ошибках (CSV)',
            'post',
            ['class' => 'btn btn-outline-danger mt-2']
        );
    }
    
    return $output;
}

/**
 * Получить читаемое название типа ошибки
 */
function get_error_type_name($type) {
    $names = [
        'mapping_error' => '🔗 Ошибки соответствия элементов оценки',
        'user_mapping_error' => '👤 Ошибки соответствия пользователей',
        'course_mapping_error' => '📚 Ошибки соответствия курсов',
        'network_error' => '🌐 Сетевые ошибки',
        'permission_error' => '🔒 Ошибки прав доступа',
        'unknown_error' => '❓ Прочие ошибки'
    ];
    
    return $names[$type] ?? '❓ Неизвестная ошибка';
}

// Обработка скачивания CSV с ошибками
if (optional_param('download_errors', 0, PARAM_INT) && $detailed_results && !empty($detailed_results['details']['errors'])) {
    $filename = 'grade_statements_errors_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // BOM для корректного отображения UTF-8 в Excel
    fwrite($output, "\xEF\xBB\xBF");
    
    // Заголовки
    fputcsv($output, [
        'Тип ошибки',
        'ID пользователя', 
        'Имя пользователя',
        'Email',
        'ID элемента',
        'Название элемента',
        'Тип элемента',
        'Оценка',
        'Макс. оценка',
        'Сообщение об ошибке',
        'Время'
    ]);
    
    // Данные
    foreach ($detailed_results['details']['errors'] as $error) {
        fputcsv($output, [
            get_error_type_name($error['error_type']),
            $error['user_id'],
            $error['user_name'],
            $error['user_email'],
            $error['item_id'],
            $error['item_name'],
            $error['item_type'],
            $error['grade_value'],
            $error['grade_max'],
            $error['error_message'],
            userdate($error['timestamp'])
        ]);
    }
    
    fclose($output);
    exit;
}

// Начинаем вывод страницы
echo $OUTPUT->header();

// Показываем сообщение если есть
if (!empty($message)) {
    echo $OUTPUT->notification($message, $message_type);
}

// Выводим описание страницы
echo $OUTPUT->box_start('generalbox');
echo $OUTPUT->heading(get_string('grade_course_selection', 'local_cdo_unti2035bas'), 3);
echo html_writer::tag('p', get_string('grade_course_selection_help', 'local_cdo_unti2035bas'));
echo $OUTPUT->box_end();

// Отображаем форму
$form->display();

// Отображаем детальные результаты если есть
if ($detailed_results) {
    echo $OUTPUT->heading('📊 Детальные результаты', 3);
    echo display_detailed_results($detailed_results);
}

// Ссылка назад
echo $OUTPUT->single_button(
    new moodle_url('/admin/settings.php', ['section' => 'local_cdo_unti2035bas']), 
    '← Назад к настройкам',
    'get',
    ['class' => 'mt-3']
);

echo $OUTPUT->footer(); 
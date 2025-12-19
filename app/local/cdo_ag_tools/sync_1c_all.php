<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Страница для массовой отправки всех оценок в 1С
 *
 * @package     local_cdo_ag_tools
 * @copyright   InQaaaa
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

use local_cdo_ag_tools\integrations\onec_integration;
use local_cdo_ag_tools\forms\sync_1c_form;

$context = context_system::instance();
require_login();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/cdo_ag_tools/sync_1c_all.php'));
$PAGE->set_title(get_string('sync_all_grades_to_1c_title', 'local_cdo_ag_tools'));
$PAGE->set_heading(get_string('sync_all_grades_to_1c_heading', 'local_cdo_ag_tools'));
$PAGE->set_pagelayout('admin');

// Создаем форму
$mform = new sync_1c_form();

// Обработка отправки формы
if ($formdata = $mform->get_data()) {
    
    // Получаем timestamp из date_selector
    $datefromTimestamp = !empty($formdata->datefrom) ? $formdata->datefrom : 0;
    
    // Выполняем массовую отправку
    $result = sync_all_grades_to_1c($datefromTimestamp);
    
    if ($result['success']) {
        \core\notification::success(get_string('sync_completed_successfully', 'local_cdo_ag_tools', $result));
    } else {
        \core\notification::error(get_string('sync_failed', 'local_cdo_ag_tools', $result['error']));
    }
    
    // Сохраняем результаты в сессии для отображения
    $SESSION->sync_result = $result;
    
    redirect($PAGE->url);
}

echo $OUTPUT->header();

// Получаем статистику
$stats = get_sync_statistics();

echo $OUTPUT->heading(get_string('sync_all_grades_to_1c_heading', 'local_cdo_ag_tools'));

// Показываем результаты последней синхронизации, если они есть
if (isset($SESSION->sync_result)) {
    $syncResult = $SESSION->sync_result;
    unset($SESSION->sync_result);
    
    $resultClass = $syncResult['success'] ? 'alert-success' : 'alert-danger';
    echo html_writer::start_tag('div', ['class' => 'alert ' . $resultClass]);
    echo html_writer::tag('h4', get_string('sync_results_title', 'local_cdo_ag_tools'));
    
    if ($syncResult['success']) {
        echo html_writer::tag('p', get_string('sync_total_processed', 'local_cdo_ag_tools', $syncResult['total_processed']));
        echo html_writer::tag('p', get_string('sync_successful_sends', 'local_cdo_ag_tools', $syncResult['successful_sends']), 
            ['style' => 'color: #155724; font-weight: bold;']);
        echo html_writer::tag('p', get_string('sync_failed_sends', 'local_cdo_ag_tools', $syncResult['failed_sends']), 
            ['style' => 'color: #721c24; font-weight: bold;']);
        
        // Вычисляем процент успешности
        if ($syncResult['total_processed'] > 0) {
            $successRate = round(($syncResult['successful_sends'] / $syncResult['total_processed']) * 100, 2);
            echo html_writer::tag('p', get_string('sync_success_rate', 'local_cdo_ag_tools', $successRate), 
                ['style' => 'font-weight: bold;']);
        }
    } else {
        echo html_writer::tag('p', get_string('sync_error_message', 'local_cdo_ag_tools', $syncResult['error']));
    }
    
    echo html_writer::end_tag('div');
}

// Показываем статистику
echo html_writer::start_tag('div', ['class' => 'alert alert-info']);
echo html_writer::tag('h4', get_string('current_statistics', 'local_cdo_ag_tools'));
echo html_writer::tag('p', get_string('total_grades_in_table', 'local_cdo_ag_tools', $stats['total_grades']));
echo html_writer::tag('p', get_string('unique_users', 'local_cdo_ag_tools', $stats['unique_users']));
echo html_writer::tag('p', get_string('unique_courses', 'local_cdo_ag_tools', $stats['unique_courses']));
echo html_writer::end_tag('div');

// Предупреждение
echo html_writer::start_tag('div', ['class' => 'alert alert-warning']);
echo html_writer::tag('h4', get_string('warning', 'local_cdo_ag_tools'));
echo html_writer::tag('p', get_string('sync_warning_message', 'local_cdo_ag_tools'));
echo html_writer::end_tag('div');

// Отображаем форму
$mform->display();

echo $OUTPUT->footer();

/**
 * Функция для синхронизации всех оценок с 1С
 * 
 * @param int $dateFrom Timestamp начальной даты (0 = все записи)
 * @return array Результат синхронизации
 */
function sync_all_grades_to_1c(int $dateFrom = 0): array {
    global $DB;
    
    // Увеличиваем лимиты для длительной операции
    @set_time_limit(0);
    raise_memory_limit(MEMORY_HUGE);
    
    $result = [
        'success' => true,
        'total_processed' => 0,
        'successful_sends' => 0,
        'failed_sends' => 0,
        'error' => null
    ];
    
    try {
        // Формируем условия выборки
        $conditions = [];
        $params = [];
        
        if ($dateFrom > 0) {
            $conditions[] = 'created_at >= :datefrom';
            $params['datefrom'] = $dateFrom;
        }
        
        $where = !empty($conditions) ? implode(' AND ', $conditions) : '';
        
        // Проверяем существование таблицы
        if (!$DB->get_manager()->table_exists('local_cdo_ag_tools_grades_1c')) {
            throw new Exception('Table local_cdo_ag_tools_grades_1c does not exist');
        }
        
        // Сначала считаем общее количество записей для статистики
        if (!empty($where)) {
            $totalCount = $DB->count_records_select('local_cdo_ag_tools_grades_1c', $where, $params);
        } else {
            $totalCount = $DB->count_records('local_cdo_ag_tools_grades_1c');
        }
        
        // Используем recordset для экономии памяти (обрабатывает по одной записи)
        if (!empty($where)) {
            $recordset = $DB->get_recordset_select('local_cdo_ag_tools_grades_1c', $where, $params, 'created_at ASC');
        } else {
            $recordset = $DB->get_recordset('local_cdo_ag_tools_grades_1c', null, 'created_at ASC');
        }
        
        foreach ($recordset as $grade) {
            $result['total_processed']++;
            
            // Подготавливаем данные для отправки в формате, ожидаемом интеграцией
            $gradeData = [
                'userid' => $grade->user_id,
                'courseid' => $grade->course_id,
                'grade' => $grade->grade,
                'itemtype' => $grade->item_type,
                'section_id' => $grade->section_id,
                'timecreated' => $grade->created_at,
                'timemodified' => $grade->updated_at
            ];
            
            try {
                // Отправляем через существующую интеграцию
                if (onec_integration::send_grade_to_1c($gradeData)) {
                    $result['successful_sends']++;
                } else {
                    $result['failed_sends']++;
                }
            } catch (Exception $e) {
                $result['failed_sends']++;
                debugging('Failed to send grade ID ' . $grade->id . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
            
            // Логируем прогресс каждые 100 записей
            if ($result['total_processed'] % 100 == 0) {
                debugging("Progress: {$result['total_processed']} / {$totalCount} processed", DEBUG_DEVELOPER);
            }
            
            // Добавляем небольшую паузу чтобы не перегружать 1С
            usleep(100000); // 0.1 секунды
        }
        
        // ВАЖНО: Закрываем recordset для освобождения ресурсов
        $recordset->close();
        
    } catch (Exception $e) {
        $result['success'] = false;
        $result['error'] = $e->getMessage();
        debugging('Error in sync_all_grades_to_1c: ' . $e->getMessage(), DEBUG_DEVELOPER);
        
        // Закрываем recordset в случае ошибки
        if (isset($recordset) && !empty($recordset)) {
            $recordset->close();
        }
    }
    
    return $result;
}

/**
 * Получить статистику по данным в таблице
 * 
 * @return array Статистика
 */
function get_sync_statistics(): array {
    global $DB;
    
    $stats = [];
    
    try {
        $stats['total_grades'] = $DB->count_records('local_cdo_ag_tools_grades_1c');
        
        $stats['unique_users'] = $DB->get_field_sql(
            'SELECT COUNT(DISTINCT user_id) FROM {local_cdo_ag_tools_grades_1c}'
        );
        
        $stats['unique_courses'] = $DB->get_field_sql(
            'SELECT COUNT(DISTINCT course_id) FROM {local_cdo_ag_tools_grades_1c}'
        );
        
    } catch (Exception $e) {
        $stats = [
            'total_grades' => 0,
            'unique_users' => 0,
            'unique_courses' => 0
        ];
    }
    
    return $stats;
}

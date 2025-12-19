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
 * Страница отчета о прогрессе просмотра видео
 *
 * @package    local_videoprogress
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_videoprogress\report_manager;

// Проверяем аутентификацию
require_login();
require_capability('moodle/site:config', context_system::instance());

// Получаем параметры
$courseid = optional_param('courseid', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

// Настройка страницы
$PAGE->set_url('/local/videoprogress/report.php', ['courseid' => $courseid]);
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('videoprogressreport', 'local_videoprogress'));
$PAGE->set_heading(get_string('videoprogressreport', 'local_videoprogress'));
$PAGE->set_pagelayout('admin');

// Навигация
admin_externalpage_setup('videoprogressreport', '', ['courseid' => $courseid]);

// Создаем менеджер отчетов
$report_manager = new report_manager();

// Обработка скачивания
if (!empty($download)) {
    $report_manager->download_report($courseid, $download);
    exit;
}

// Получаем данные отчета
$report_data = $report_manager->get_report_data($courseid);
$courses = $report_manager->get_available_courses();

// Настройка рендерера
$renderer = $PAGE->get_renderer('local_videoprogress');

echo $OUTPUT->header();

// Отображаем форму выбора курса
echo $renderer->render_course_selector($courses, $courseid);

// Отображаем отчет, если выбран курс
if ($courseid > 0 && !empty($report_data)) {
    echo $renderer->render_video_progress_report($report_data, $courseid);
} elseif ($courseid > 0) {
    echo $OUTPUT->notification(get_string('nodata', 'local_videoprogress'), 'info');
}

echo $OUTPUT->footer(); 
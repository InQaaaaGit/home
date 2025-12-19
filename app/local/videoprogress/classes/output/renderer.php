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

namespace local_videoprogress\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use moodle_url;
use html_writer;

/**
 * Renderer для плагина local_videoprogress
 *
 * @package    local_videoprogress
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Отобразить селектор курса
     *
     * @param array $courses Список курсов
     * @param int $selectedcourseid Выбранный курс
     * @return string HTML
     */
    public function render_course_selector(array $courses, int $selectedcourseid = 0): string {
        global $PAGE;

        $html = html_writer::start_div('course-selector mb-3');
        $html .= html_writer::start_tag('form', [
            'method' => 'get',
            'action' => $PAGE->url->out_omit_querystring(),
            'class' => 'form-inline'
        ]);

        $html .= html_writer::label(
            get_string('selectcourse', 'local_videoprogress') . ': ',
            'courseid',
            false,
            ['class' => 'mr-2']
        );

        $options = [0 => get_string('choosecourse', 'local_videoprogress')];
        foreach ($courses as $course) {
            $options[$course->id] = $course->fullname;
        }

        $html .= html_writer::select(
            $options,
            'courseid',
            $selectedcourseid,
            false,
            ['id' => 'courseid', 'class' => 'form-control mr-2']
        );

        $html .= html_writer::empty_tag('input', [
            'type' => 'submit',
            'value' => get_string('show'),
            'class' => 'btn btn-primary'
        ]);

        $html .= html_writer::end_tag('form');
        $html .= html_writer::end_div();

        // Добавляем JavaScript для автоматической отправки формы
        $this->page->requires->js_amd_inline('
            require(["jquery"], function($) {
                $("#courseid").change(function() {
                    $(this).closest("form").submit();
                });
            });
        ');

        return $html;
    }

    /**
     * Отобразить отчет о прогрессе видео
     *
     * @param array $report_data Данные отчета
     * @param int $courseid ID курса
     * @return string HTML
     */
    public function render_video_progress_report(array $report_data, int $courseid): string {
        global $PAGE;

        if (empty($report_data['users']) || empty($report_data['videos'])) {
            return $this->output->notification(get_string('nodata', 'local_videoprogress'), 'info');
        }

        $html = '';

        // Кнопки экспорта
        $html .= $this->render_export_buttons($courseid);

        // Заголовок таблицы
        $html .= html_writer::start_div('table-responsive');
        $html .= html_writer::start_tag('table', [
            'class' => 'table table-striped table-bordered video-progress-report',
            'id' => 'video-progress-table'
        ]);

        // Заголовки
        $html .= html_writer::start_tag('thead', ['class' => 'thead-dark']);
        $html .= html_writer::start_tag('tr');
        $html .= html_writer::tag('th', get_string('student', 'local_videoprogress'), 
            ['class' => 'sticky-column']);
        
        foreach ($report_data['videos'] as $unique_key => $video) {
            $html .= html_writer::tag('th', 
                html_writer::tag('div', $video['name'], ['class' => 'video-header']),
                ['class' => 'video-column', 'data-videoid' => $video['id'], 'data-uniquekey' => $unique_key]
            );
        }
        $html .= html_writer::end_tag('tr');
        $html .= html_writer::end_tag('thead');

        // Тело таблицы
        $html .= html_writer::start_tag('tbody');
        foreach ($report_data['users'] as $user) {
            $html .= html_writer::start_tag('tr', ['data-userid' => $user->id]);
            
            // Имя студента
            $student_name = fullname($user);
            $html .= html_writer::tag('td', $student_name, ['class' => 'sticky-column student-name']);
            
            // Прогресс по каждому видео
            foreach ($report_data['videos'] as $unique_key => $video) {
                $progress_data = $report_data['progress'][$user->id][$unique_key] ?? null;
                $progress = $progress_data ? $progress_data['progress'] : 0;
                $timemodified = $progress_data ? $progress_data['timemodified'] : null;
                
                $cell_class = $this->get_progress_cell_class($progress);
                $cell_content = $this->format_progress_cell($progress, $timemodified);
                
                $html .= html_writer::tag('td', $cell_content, [
                    'class' => "progress-cell $cell_class",
                    'data-progress' => $progress,
                    'data-videoid' => $video['id'],
                    'data-uniquekey' => $unique_key
                ]);
            }
            
            $html .= html_writer::end_tag('tr');
        }
        $html .= html_writer::end_tag('tbody');
        $html .= html_writer::end_tag('table');
        $html .= html_writer::end_div();

        // Добавляем CSS и JavaScript
        $this->add_report_assets();

        return $html;
    }

    /**
     * Отобразить кнопки экспорта
     *
     * @param int $courseid ID курса
     * @return string HTML
     */
    private function render_export_buttons(int $courseid): string {
        $html = html_writer::start_div('export-buttons mb-3');
        $html .= html_writer::tag('h4', get_string('export', 'local_videoprogress'), ['class' => 'd-inline mr-3']);

        // Кнопка CSV
        $csv_url = new moodle_url('/local/videoprogress/report.php', [
            'courseid' => $courseid,
            'download' => 'csv'
        ]);
        $html .= html_writer::link($csv_url, get_string('exportcsv', 'local_videoprogress'), [
            'class' => 'btn btn-outline-primary btn-sm mr-2'
        ]);

        // Кнопка Excel
        $excel_url = new moodle_url('/local/videoprogress/report.php', [
            'courseid' => $courseid,
            'download' => 'excel'
        ]);
        $html .= html_writer::link($excel_url, get_string('exportexcel', 'local_videoprogress'), [
            'class' => 'btn btn-outline-success btn-sm'
        ]);

        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Получить CSS класс для ячейки прогресса
     *
     * @param float $progress Прогресс в процентах
     * @return string CSS класс
     */
    private function get_progress_cell_class(float $progress): string {
        if ($progress >= 90) {
            return 'progress-excellent';
        } elseif ($progress >= 70) {
            return 'progress-good';
        } elseif ($progress >= 40) {
            return 'progress-average';
        } elseif ($progress > 0) {
            return 'progress-poor';
        } else {
            return 'progress-none';
        }
    }

    /**
     * Форматировать содержимое ячейки прогресса
     *
     * @param float $progress Прогресс в процентах
     * @param int|null $timemodified Время последнего обновления
     * @return string HTML содержимое ячейки
     */
    private function format_progress_cell(float $progress, ?int $timemodified): string {
        $progress_text = $progress > 0 ? round($progress, 1) . '%' : '—';
        
        $html = html_writer::tag('div', $progress_text, ['class' => 'progress-value']);
        
        if ($timemodified && $progress > 0) {
            $date_str = userdate($timemodified, get_string('strftimedatefullshort'));
            $html .= html_writer::tag('small', $date_str, [
                'class' => 'progress-date text-muted d-block'
            ]);
        }
        
        return $html;
    }

    /**
     * Добавить CSS и JavaScript для отчета
     */
    private function add_report_assets(): void {
        // Добавляем CSS и JavaScript через один AMD модуль
        $this->page->requires->js_amd_inline('
            require(["jquery"], function($) {
                // Добавляем CSS стили
                var css = `
                .video-progress-report {
                    font-size: 0.9em;
                }
                .sticky-column {
                    position: sticky;
                    left: 0;
                    background-color: #f8f9fa;
                    z-index: 10;
                    min-width: 150px;
                    max-width: 200px;
                }
                .video-column {
                    min-width: 100px;
                    text-align: center;
                }
                .video-header {
                    writing-mode: vertical-rl;
                    text-orientation: mixed;
                    max-width: 80px;
                    word-break: break-word;
                }
                .progress-cell {
                    text-align: center;
                    padding: 8px 4px;
                }
                .progress-excellent { background-color: #d4edda; color: #155724; }
                .progress-good { background-color: #d1ecf1; color: #0c5460; }
                .progress-average { background-color: #fff3cd; color: #856404; }
                .progress-poor { background-color: #f8d7da; color: #721c24; }
                .progress-none { background-color: #f6f6f6; color: #6c757d; }
                .progress-value { font-weight: bold; }
                .progress-date { font-size: 0.75em; }
                .export-buttons { border-bottom: 1px solid #dee2e6; padding-bottom: 15px; }
                `;
                
                // Добавляем CSS в head
                if (!document.getElementById("videoprogress-report-styles")) {
                    var style = document.createElement("style");
                    style.id = "videoprogress-report-styles";
                    style.textContent = css;
                    document.head.appendChild(style);
                }
                
                // Tooltip для ячеек с прогрессом
                $(".progress-cell[data-progress]").each(function() {
                    var progress = $(this).data("progress");
                    var videoid = $(this).data("videoid");
                    if (progress > 0) {
                        $(this).attr("title", "Прогресс: " + progress + "%");
                    }
                });
                
                // Подсветка строки при наведении
                $("#video-progress-table tbody tr").hover(
                    function() { $(this).addClass("table-active"); },
                    function() { $(this).removeClass("table-active"); }
                );
            });
        ');
    }

    /**
     * Render the video tracker
     *
     * @param video_tracker $tracker
     * @return string HTML output
     */
    public function render_video_tracker(video_tracker $tracker): string {
        $data = $tracker->export_for_template($this);
        return $this->render_from_template('local_videoprogress/video_tracker', $data);
    }
} 
<?php

namespace local_cdo_ag_tools\digest;

use dml_exception;
use moodle_exception;
use stdClass;
use html_writer;
use moodle_url;

/**
 * Класс для генерации дайджеста оценок пользователя
 *
 * @package    local_cdo_ag_tools
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_digest {
    /**
     * @var int ID пользователя
     */
    private int $userId;

    /**
     * @var int|null Начало периода (timestamp)
     */
    private ?int $dateFrom;

    /**
     * @var int|null Конец периода (timestamp)
     */
    private ?int $dateTo;

    /**
     * Конструктор класса
     *
     * @param int $userId ID пользователя
     * @param int|null $dateFrom Начало периода (timestamp)
     * @param int|null $dateTo Конец периода (timestamp)
     */
    public function __construct(int $userId, ?int $dateFrom = null, ?int $dateTo = null) {
        $this->userId = $userId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    /**
     * Получает оценки пользователя за указанный период
     *
     * @return array Массив оценок
     * @throws dml_exception
     */
    private function get_user_grades(): array {
        global $DB;

        $conditions = ['userid' => $this->userId];
        $sql = "SELECT * FROM {local_cdo_ag_grade_notifications} WHERE userid = :userid";
        $params = ['userid' => $this->userId];

        if ($this->dateFrom !== null) {
            $sql .= " AND timecreated >= :datefrom";
            $params['datefrom'] = $this->dateFrom;
        }

        if ($this->dateTo !== null) {
            $sql .= " AND timecreated <= :dateto";
            $params['dateto'] = $this->dateTo;
        }

        $sql .= " ORDER BY timecreated DESC";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Группирует оценки по курсам
     *
     * @param array $grades Массив оценок
     * @return array Массив оценок, сгруппированных по курсам
     * @throws dml_exception
     */
    private function group_grades_by_course(array $grades): array {
        global $DB;

        $grouped = [];

        foreach ($grades as $grade) {
            $courseid = $grade->courseid;

            if (!isset($grouped[$courseid])) {
                $course = $DB->get_record('course', ['id' => $courseid], 'id, fullname, shortname');
                $grouped[$courseid] = [
                    'course' => $course,
                    'grades' => [],
                ];
            }

            $grouped[$courseid]['grades'][] = $grade;
        }

        return $grouped;
    }

    /**
     * Вычисляет статистику по оценкам
     *
     * @param array $grades Массив оценок
     * @return stdClass Объект со статистикой
     */
    private function calculate_statistics(array $grades): stdClass {
        $stats = new stdClass();
        $stats->total = count($grades);
        $stats->average = 0;
        $stats->max = 0;
        $stats->min = 0;

        if ($stats->total > 0) {
            $sum = 0;
            $gradeValues = [];

            foreach ($grades as $grade) {
                $sum += $grade->grade;
                $gradeValues[] = $grade->grade;
            }

            $stats->average = round($sum / $stats->total, 2);
            $stats->max = max($gradeValues);
            $stats->min = min($gradeValues);
        }

        return $stats;
    }

    /**
     * Генерирует HTML для отображения статистики
     *
     * @param stdClass $stats Объект со статистикой
     * @return string HTML код
     */
    private function render_statistics(stdClass $stats): string {
        $html = html_writer::start_div('grade-digest-statistics');
        $html .= html_writer::tag('h3', get_string('statistics', 'local_cdo_ag_tools'));

        $html .= html_writer::start_tag('div', ['class' => 'statistics-grid']);

        // Общее количество оценок
        $html .= html_writer::start_div('stat-item');
        $html .= html_writer::tag('div', get_string('total_grades', 'local_cdo_ag_tools'), ['class' => 'stat-label']);
        $html .= html_writer::tag('div', $stats->total, ['class' => 'stat-value']);
        $html .= html_writer::end_div();

        // Средняя оценка
        $html .= html_writer::start_div('stat-item');
        $html .= html_writer::tag('div', get_string('average_grade', 'local_cdo_ag_tools'), ['class' => 'stat-label']);
        $html .= html_writer::tag('div', $stats->average, ['class' => 'stat-value']);
        $html .= html_writer::end_div();

        // Максимальная оценка
        $html .= html_writer::start_div('stat-item');
        $html .= html_writer::tag('div', get_string('max_grade', 'local_cdo_ag_tools'), ['class' => 'stat-label']);
        $html .= html_writer::tag('div', $stats->max, ['class' => 'stat-value']);
        $html .= html_writer::end_div();

        // Минимальная оценка
        $html .= html_writer::start_div('stat-item');
        $html .= html_writer::tag('div', get_string('min_grade', 'local_cdo_ag_tools'), ['class' => 'stat-label']);
        $html .= html_writer::tag('div', $stats->min, ['class' => 'stat-value']);
        $html .= html_writer::end_div();

        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Получает информацию о модуле (секция и cmid)
     *
     * @param int $courseid ID курса
     * @param string $modulename Название модуля
     * @param string $moduletype Тип модуля
     * @return object|null Объект с данными модуля (sectionname, cmid) или null
     */
    private function get_module_info(int $courseid, string $modulename, string $moduletype): ?object {
        global $DB;

        try {
            // Проверяем, что тип модуля содержит только буквы и цифры (защита от SQL инъекций)
            if (!preg_match('/^[a-z0-9]+$/i', $moduletype)) {
                return null;
            }

            // Проверяем, существует ли таблица для данного типа модуля
            $tables = $DB->get_tables();
            if (!in_array($moduletype, $tables)) {
                return null;
            }

            // Формируем SQL запрос с безопасным именем таблицы
            $tableName = '{' . $moduletype . '}';
            $sql = "SELECT cm.id as cmid, cm.section as sectionid
                    FROM {course_modules} cm
                    INNER JOIN {modules} m ON m.id = cm.module
                    INNER JOIN {$tableName} mod ON mod.id = cm.instance
                    WHERE cm.course = :courseid
                      AND m.name = :moduletype
                      AND " . $DB->sql_compare_text('mod.name') . " = " . $DB->sql_compare_text(':modulename') . "
                    LIMIT 1";

            $params = [
                'courseid' => $courseid,
                'moduletype' => $moduletype,
                'modulename' => $modulename,
            ];

            $cm = $DB->get_record_sql($sql, $params);

            if ($cm) {
                $result = new \stdClass();
                $result->cmid = $cm->cmid;
                $result->sectionname = '';

                // Получаем информацию о курсе и его секциях через modinfo (правильный способ в Moodle)
                if ($cm->sectionid) {
                    try {
                        $modinfo = get_fast_modinfo($courseid);
                        $sections = $modinfo->get_section_info_all();
                        
                        foreach ($sections as $sectioninfo) {
                            if ($sectioninfo->id == $cm->sectionid) {
                                // Если у секции есть название, используем его
                                if (!empty($sectioninfo->name)) {
                                    $result->sectionname = format_string($sectioninfo->name);
                                } else {
                                    // Иначе возвращаем "Тема N"
                                    $result->sectionname = get_string('section', 'local_cdo_ag_tools') . ' ' . $sectioninfo->section;
                                }
                                break;
                            }
                        }
                    } catch (\Exception $e) {
                        // Если modinfo не работает, пытаемся через прямой запрос
                        $section = $DB->get_record('course_sections', ['id' => $cm->sectionid], 'id, name, section');
                        if ($section) {
                            if (!empty($section->name)) {
                                $result->sectionname = format_string($section->name);
                            } else {
                                $result->sectionname = get_string('section', 'local_cdo_ag_tools') . ' ' . $section->section;
                            }
                        }
                    }
                }

                return $result;
            }
        } catch (\Exception $e) {
            // В случае ошибки возвращаем null
            return null;
        }

        return null;
    }

    /**
     * Генерирует HTML для таблицы оценок курса
     *
     * @param array $grades Массив оценок
     * @return string HTML код
     */
    private function render_course_grades_table(array $grades): string {
        $table = new \html_table();
        $table->head = [
            get_string('section_topic', 'local_cdo_ag_tools'),
            get_string('module_name', 'local_cdo_ag_tools'),
            get_string('module_type', 'local_cdo_ag_tools'),
            get_string('grade', 'local_cdo_ag_tools'),
            get_string('date', 'local_cdo_ag_tools'),
        ];
        $table->attributes['class'] = 'generaltable grade-digest-table';
        $table->data = [];

        // Группируем оценки по курсу для оптимизации
        $gradesByCourse = [];
        foreach ($grades as $grade) {
            if (!isset($gradesByCourse[$grade->courseid])) {
                $gradesByCourse[$grade->courseid] = [];
            }
            $gradesByCourse[$grade->courseid][] = $grade;
        }

        // Обрабатываем каждый курс
        foreach ($gradesByCourse as $courseid => $courseGrades) {
            // Получаем modinfo для курса один раз
            try {
                $modinfo = get_fast_modinfo($courseid);
                $cms = $modinfo->get_cms();
                $sections = $modinfo->get_section_info_all();
                
                // Создаем карту секций
                $sectionMap = [];
                foreach ($sections as $sectioninfo) {
                    $sectionMap[$sectioninfo->section] = $sectioninfo;
                }
            } catch (\Exception $e) {
                $modinfo = null;
                $cms = [];
                $sectionMap = [];
            }

            // Обрабатываем каждую оценку курса
            foreach ($courseGrades as $grade) {
                $row = [];
                $sectionName = '';
                $cmid = null;

                // Ищем модуль по имени и типу
                if ($modinfo && !empty($cms)) {
                    foreach ($cms as $cm) {
                        if ($cm->modname == $grade->moduletype && 
                            trim($cm->name) == trim($grade->modulename)) {
                            $cmid = $cm->id;
                            
                            // Получаем название секции
                            if (isset($sectionMap[$cm->sectionnum])) {
                                $sectioninfo = $sectionMap[$cm->sectionnum];
                                if (!empty($sectioninfo->name)) {
                                    $sectionName = format_string($sectioninfo->name);
                                } else {
                                    $sectionName = get_string('section', 'local_cdo_ag_tools') . ' ' . $sectioninfo->section;
                                }
                            }
                            break;
                        }
                    }
                }

                // Секция
                if (!empty($sectionName)) {
                    $row[] = $sectionName;
                } else {
                    $row[] = html_writer::tag('em', '—', ['class' => 'text-muted']);
                }
                
                // Название модуля (с ссылкой, если есть cmid)
                $moduleName = format_string($grade->modulename);
                if ($cmid) {
                    $moduleUrl = new moodle_url('/mod/' . $grade->moduletype . '/view.php', ['id' => $cmid]);
                    $row[] = html_writer::link($moduleUrl, $moduleName, ['target' => '_blank']);
                } else {
                    $row[] = $moduleName;
                }
                
                $row[] = format_string($grade->moduletype);
                $row[] = $this->format_grade($grade->grade);
                $row[] = userdate($grade->timecreated, '%d.%m.%Y');
                $table->data[] = $row;
            }
        }

        return html_writer::table($table);
    }

    /**
     * Форматирует оценку с цветовой индикацией
     *
     * @param float $grade Оценка
     * @return string HTML код с форматированной оценкой
     */
    private function format_grade(float $grade): string {
        $class = 'grade-value';

        if ($grade >= 80) {
            $class .= ' grade-excellent';
        } elseif ($grade >= 60) {
            $class .= ' grade-good';
        } elseif ($grade >= 40) {
            $class .= ' grade-satisfactory';
        } else {
            $class .= ' grade-poor';
        }

        return html_writer::tag('span', $grade, ['class' => $class]);
    }

    /**
     * Генерирует HTML дайджест оценок пользователя
     *
     * @return string HTML код дайджеста
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function generate_html_digest(): string {
        global $DB, $OUTPUT;

        // Получаем данные пользователя
        $user = $DB->get_record('user', ['id' => $this->userId], '*', MUST_EXIST);
        $displayname = !empty($user->alternatename)
            ? format_string($user->alternatename)
            : fullname($user);

        $html = html_writer::start_div('grade-digest-container');

        // Заголовок дайджеста
        $html .= html_writer::tag('h2', get_string('grade_digest', 'local_cdo_ag_tools'));

        // Информация о пользователе
        $html .= html_writer::start_div('digest-user-info');
        $html .= html_writer::tag('p',
            get_string('digest_for_user', 'local_cdo_ag_tools', $displayname),
            ['class' => 'user-fullname']
        );

        // Период, если указан
        if ($this->dateFrom !== null || $this->dateTo !== null) {
            $periodText = $this->get_period_text();
            $html .= html_writer::tag('p', $periodText, ['class' => 'digest-period']);
        }
        $html .= html_writer::end_div();

        // Получаем оценки
        $grades = $this->get_user_grades();

        if (empty($grades)) {
            $html .= html_writer::tag('p',
                get_string('no_grades_found', 'local_cdo_ag_tools'),
                ['class' => 'alert alert-info']
            );
            $html .= html_writer::end_div();
            return $html;
        }

        // Статистика
        $stats = $this->calculate_statistics($grades);
        $html .= $this->render_statistics($stats);

        // Оценки по курсам
        $groupedGrades = $this->group_grades_by_course($grades);

        $html .= html_writer::start_div('grade-digest-courses');
        $html .= html_writer::tag('h3', get_string('grades_by_courses', 'local_cdo_ag_tools'));

        foreach ($groupedGrades as $courseid => $courseData) {
            $html .= html_writer::start_div('course-section', ['data-courseid' => $courseid]);

            // Название курса
            $courseUrl = new moodle_url('/course/view.php', ['id' => $courseid]);
            $courseLink = html_writer::link($courseUrl, format_string($courseData['course']->fullname));
            $html .= html_writer::tag('h4', $courseLink, ['class' => 'course-title']);

            // Таблица оценок для курса
            $html .= $this->render_course_grades_table($courseData['grades']);

            $html .= html_writer::end_div();
        }

        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Получает текстовое описание периода
     *
     * @return string Текст периода
     */
    private function get_period_text(): string {
        $parts = [];

        if ($this->dateFrom !== null) {
            $parts[] = get_string('from', 'local_cdo_ag_tools') . ' ' .
                userdate($this->dateFrom, '%d.%m.%Y');
        }

        if ($this->dateTo !== null) {
            $parts[] = get_string('to', 'local_cdo_ag_tools') . ' ' .
                userdate($this->dateTo, '%d.%m.%Y');
        }

        return implode(' ', $parts);
    }

    /**
     * Устанавливает период для выборки оценок
     *
     * @param int|null $dateFrom Начало периода (timestamp)
     * @param int|null $dateTo Конец периода (timestamp)
     * @return self
     */
    public function set_period(?int $dateFrom, ?int $dateTo): self {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        return $this;
    }

    /**
     * Устанавливает период "последние N дней"
     *
     * @param int $days Количество дней
     * @return self
     */
    public function set_last_days(int $days): self {
        $this->dateTo = time();
        $this->dateFrom = strtotime("-{$days} days");
        return $this;
    }

    /**
     * Устанавливает период "текущий месяц"
     *
     * @return self
     */
    public function set_current_month(): self {
        $this->dateFrom = strtotime('first day of this month 00:00:00');
        $this->dateTo = strtotime('last day of this month 23:59:59');
        return $this;
    }

    /**
     * Устанавливает период "текущий год"
     *
     * @return self
     */
    public function set_current_year(): self {
        $this->dateFrom = strtotime('first day of january this year 00:00:00');
        $this->dateTo = strtotime('last day of december this year 23:59:59');
        return $this;
    }
}


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

namespace local_videoprogress;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/csvlib.class.php');

/**
 * Класс для управления отчетами о прогрессе видео
 *
 * @package    local_videoprogress
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_manager {

    /**
     * Получить данные отчета для курса
     *
     * @param int $courseid ID курса (-1 для всех курсов)
     * @return array Данные отчета
     */
    public function get_report_data(int $courseid): array {
        global $DB;

        if ($courseid <= 0 && $courseid !== -1) {
            return [];
        }

        if ($courseid === -1) {
            // Получаем данные для всех курсов
            return $this->get_all_courses_report_data();
        }

        // Получаем всех пользователей, которые смотрели видео в курсе
        $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email
                FROM {user} u
                JOIN {local_videoprogress} vp ON vp.userid = u.id
                JOIN {course_modules} cm ON cm.id = vp.cmid
                WHERE cm.course = :courseid
                AND u.deleted = 0
                ORDER BY u.lastname, u.firstname";

        $users = $DB->get_records_sql($sql, ['courseid' => $courseid]);

        if (empty($users)) {
            return [];
        }

        // Получаем все видео в курсе
        $videos = $this->get_course_videos($courseid);

        // Получаем прогресс для всех пользователей
        $progress_data = $this->get_progress_matrix($courseid, array_keys($users), $videos);

        return [
            'users' => $users,
            'videos' => $videos,
            'progress' => $progress_data,
            'courseid' => $courseid
        ];
    }

    /**
     * Получить данные отчета для всех курсов
     *
     * @return array Данные отчета
     */
    private function get_all_courses_report_data(): array {
        global $DB;

        // Получаем всех пользователей, которые смотрели видео
        $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email
                FROM {user} u
                JOIN {local_videoprogress} vp ON vp.userid = u.id
                WHERE u.deleted = 0
                ORDER BY u.lastname, u.firstname";

        $users = $DB->get_records_sql($sql);

        if (empty($users)) {
            return [];
        }

        // Получаем все видео со всех курсов
        $videos = $this->get_all_videos();

        // Получаем прогресс для всех пользователей и видео
        $progress_data = $this->get_all_progress_matrix(array_keys($users), $videos);

        return [
            'users' => $users,
            'videos' => $videos,
            'progress' => $progress_data,
            'courseid' => -1
        ];
    }

    /**
     * Получить список видео в курсе
     *
     * @param int $courseid ID курса
     * @return array Список видео
     */
    private function get_course_videos(int $courseid): array {
        global $DB;

        $sql = "SELECT DISTINCT vp.videoid, vp.cmid, cm.instance, m.name as modname
                FROM {local_videoprogress} vp
                JOIN {course_modules} cm ON cm.id = vp.cmid
                JOIN {modules} m ON m.id = cm.module
                WHERE cm.course = :courseid
                ORDER BY vp.videoid";

        $video_records = $DB->get_records_sql($sql, ['courseid' => $courseid]);
        
        $videos = [];
        foreach ($video_records as $record) {
            // Получаем название модуля курса
            $module_name = $this->get_module_name($record->cmid, $record->modname, $record->instance);
            
            // Создаем уникальный ключ из cmid и videoid для избежания перезаписи
            $unique_key = $record->cmid . '_' . $record->videoid;
            
            $videos[$unique_key] = [
                'id' => $record->videoid,
                'name' => $module_name,
                'cmid' => $record->cmid,
                'unique_key' => $unique_key
            ];
        }

        return $videos;
    }

    /**
     * Получить все видео со всех курсов
     *
     * @return array Список видео
     */
    private function get_all_videos(): array {
        global $DB;

        $sql = "SELECT DISTINCT vp.videoid, vp.cmid, cm.instance, m.name as modname, 
                       c.shortname as course_shortname
                FROM {local_videoprogress} vp
                JOIN {course_modules} cm ON cm.id = vp.cmid
                JOIN {modules} m ON m.id = cm.module
                JOIN {course} c ON c.id = cm.course
                ORDER BY c.shortname, vp.videoid";

        $video_records = $DB->get_records_sql($sql);
        
        $videos = [];
        foreach ($video_records as $record) {
            // Получаем название модуля курса
            $module_name = $this->get_module_name($record->cmid, $record->modname, $record->instance);
            
            // Создаем уникальный ключ из cmid и videoid
            $unique_key = $record->cmid . '_' . $record->videoid;
            
            // Добавляем курс к названию для различения
            $display_name = "[{$record->course_shortname}] " . $module_name;
            
            $videos[$unique_key] = [
                'id' => $record->videoid,
                'name' => $display_name,
                'cmid' => $record->cmid,
                'unique_key' => $unique_key
            ];
        }

        return $videos;
    }

    /**
     * Получить название модуля курса
     *
     * @param int $cmid ID модуля курса
     * @param string $modname Название типа модуля
     * @param int $instance ID экземпляра модуля
     * @return string Название модуля (всегда возвращает строку)
     */
    private function get_module_name(int $cmid, string $modname, int $instance): string {
        global $DB;

        try {
            // Сначала пробуем получить название из course_modules
            $cm = $DB->get_record('course_modules', ['id' => $cmid], 'id, idnumber');
            if ($cm && !empty($cm->idnumber)) {
                return $cm->idnumber;
            }

            // Затем пробуем получить из таблицы модуля
            if ($DB->get_manager()->table_exists($modname)) {
                $module_record = $DB->get_record($modname, ['id' => $instance], 'name, intro');
                if ($module_record) {
                    // Пробуем name
                    if (!empty($module_record->name)) {
                        return $module_record->name;
                    }
                    // Если name пустое, пробуем intro (для некоторых типов модулей)
                    if (!empty($module_record->intro)) {
                        return strip_tags(substr($module_record->intro, 0, 50)) . '...';
                    }
                }
            }

            // Попробуем получить информацию из контекста модуля
            $context = \context_module::instance($cmid, IGNORE_MISSING);
            if ($context) {
                $module_info = get_fast_modinfo($context->get_course_context()->instanceid)->get_cm($cmid);
                if ($module_info && !empty($module_info->name)) {
                    return $module_info->name;
                }
            }
        } catch (\Exception $e) {
            // Логируем ошибку для диагностики
            error_log("Error getting module name for CMID $cmid: " . $e->getMessage());
        }

        // Возвращаем fallback название
        return "Module $cmid ($modname)";
    }

    /**
     * Получить матрицу прогресса пользователей по видео
     *
     * @param int $courseid ID курса
     * @param array $userids Массив ID пользователей
     * @param array $videos Массив видео с уникальными ключами
     * @return array Матрица прогресса
     */
    private function get_progress_matrix(int $courseid, array $userids, array $videos): array {
        global $DB;

        if (empty($userids) || empty($videos)) {
            return [];
        }

        // Создаем именованные параметры для пользователей
        $user_params = [];
        $user_placeholders = [];
        foreach ($userids as $index => $userid) {
            $param_name = "userid{$index}";
            $user_params[$param_name] = $userid;
            $user_placeholders[] = ":{$param_name}";
        }

        // Получаем все данные прогресса для пользователей в курсе
        $sql = "SELECT vp.id, vp.userid, vp.videoid, vp.cmid, vp.progress, vp.timemodified
                FROM {local_videoprogress} vp
                JOIN {course_modules} cm ON cm.id = vp.cmid
                WHERE cm.course = :courseid
                AND vp.userid IN (" . implode(',', $user_placeholders) . ")";

        $params = array_merge(['courseid' => $courseid], $user_params);
        $progress_records = $DB->get_records_sql($sql, $params);

        // Создаем карту cmid_videoid для быстрого поиска
        $video_map = [];
        foreach ($videos as $unique_key => $video) {
            $video_map[$video['cmid'] . '_' . $video['id']] = $unique_key;
        }

        $matrix = [];
        foreach ($progress_records as $record) {
            $lookup_key = $record->cmid . '_' . $record->videoid;
            if (isset($video_map[$lookup_key])) {
                $unique_key = $video_map[$lookup_key];
                $matrix[$record->userid][$unique_key] = [
                    'progress' => round($record->progress, 2),
                    'timemodified' => $record->timemodified
                ];
            }
        }

        return $matrix;
    }

    /**
     * Получить матрицу прогресса для всех курсов
     *
     * @param array $userids Массив ID пользователей
     * @param array $videos Массив видео с уникальными ключами
     * @return array Матрица прогресса
     */
    private function get_all_progress_matrix(array $userids, array $videos): array {
        global $DB;

        if (empty($userids) || empty($videos)) {
            return [];
        }

        // Создаем именованные параметры для пользователей
        $user_params = [];
        $user_placeholders = [];
        foreach ($userids as $index => $userid) {
            $param_name = "userid{$index}";
            $user_params[$param_name] = $userid;
            $user_placeholders[] = ":{$param_name}";
        }

        // Получаем все данные прогресса для пользователей
        $sql = "SELECT vp.id, vp.userid, vp.videoid, vp.cmid, vp.progress, vp.timemodified
                FROM {local_videoprogress} vp
                WHERE vp.userid IN (" . implode(',', $user_placeholders) . ")";

        $progress_records = $DB->get_records_sql($sql, $user_params);

        // Создаем карту cmid_videoid для быстрого поиска
        $video_map = [];
        foreach ($videos as $unique_key => $video) {
            $video_map[$video['cmid'] . '_' . $video['id']] = $unique_key;
        }

        $matrix = [];
        foreach ($progress_records as $record) {
            $lookup_key = $record->cmid . '_' . $record->videoid;
            if (isset($video_map[$lookup_key])) {
                $unique_key = $video_map[$lookup_key];
                $matrix[$record->userid][$unique_key] = [
                    'progress' => round($record->progress, 2),
                    'timemodified' => $record->timemodified
                ];
            }
        }

        return $matrix;
    }

    /**
     * Получить список доступных курсов
     *
     * @return array Список курсов
     */
    public function get_available_courses(): array {
        global $DB;

        $sql = "SELECT DISTINCT c.id, c.fullname, c.shortname
                FROM {course} c
                JOIN {course_modules} cm ON cm.course = c.id
                JOIN {local_videoprogress} vp ON vp.cmid = cm.id
                WHERE c.visible = 1
                ORDER BY c.fullname";

        $courses = $DB->get_records_sql($sql);
        
        // Добавляем опцию "Все курсы" в начало списка
        $all_courses_option = (object)[
            'id' => -1,
            'fullname' => get_string('allcourses', 'local_videoprogress'),
            'shortname' => 'all'
        ];
        
        return [-1 => $all_courses_option] + $courses;
    }

    /**
     * Скачать отчет в указанном формате
     *
     * @param int $courseid ID курса
     * @param string $format Формат файла (csv, excel)
     */
    public function download_report(int $courseid, string $format): void {
        $report_data = $this->get_report_data($courseid);
        
        if (empty($report_data)) {
            throw new \moodle_exception('nodata', 'local_videoprogress');
        }

        $course = get_course($courseid);
        $filename = clean_filename("video_progress_{$course->shortname}_" . date('Y-m-d'));

        switch ($format) {
            case 'csv':
                $this->download_csv($report_data, $filename);
                break;
            case 'excel':
                $this->download_excel($report_data, $filename);
                break;
            default:
                throw new \moodle_exception('invalidformat', 'local_videoprogress');
        }
    }

    /**
     * Скачать отчет в формате CSV
     *
     * @param array $report_data Данные отчета
     * @param string $filename Имя файла
     */
    private function download_csv(array $report_data, string $filename): void {
        $csvexport = new \csv_export_writer();
        $csvexport->set_filename($filename);

        // Заголовки
        $headers = [
            get_string('student', 'local_videoprogress'),
            get_string('email')
        ];
        
        foreach ($report_data['videos'] as $video) {
            $headers[] = $video['name'];
        }
        
        $csvexport->add_data($headers);

        // Данные
        foreach ($report_data['users'] as $user) {
            $row = [
                fullname($user),
                $user->email
            ];
            
            foreach ($report_data['videos'] as $videoid => $video) {
                $progress = $report_data['progress'][$user->id][$videoid]['progress'] ?? 0;
                $row[] = $progress . '%';
            }
            
            $csvexport->add_data($row);
        }

        $csvexport->download_file();
    }

    /**
     * Скачать отчет в формате Excel
     *
     * @param array $report_data Данные отчета
     * @param string $filename Имя файла
     */
    private function download_excel(array $report_data, string $filename): void {
        global $CFG;
        require_once($CFG->libdir . '/excellib.class.php');

        $workbook = new \MoodleExcelWorkbook($filename);
        $worksheet = $workbook->add_worksheet(get_string('videoprogressreport', 'local_videoprogress'));

        // Заголовки
        $col = 0;
        $worksheet->write_string(0, $col++, get_string('student', 'local_videoprogress'));
        $worksheet->write_string(0, $col++, get_string('email'));
        
        foreach ($report_data['videos'] as $video) {
            $worksheet->write_string(0, $col++, $video['name']);
        }

        // Данные
        $row = 1;
        foreach ($report_data['users'] as $user) {
            $col = 0;
            $worksheet->write_string($row, $col++, fullname($user));
            $worksheet->write_string($row, $col++, $user->email);
            
            foreach ($report_data['videos'] as $videoid => $video) {
                $progress = $report_data['progress'][$user->id][$videoid]['progress'] ?? 0;
                $worksheet->write_number($row, $col++, $progress);
            }
            
            $row++;
        }

        $workbook->close();
    }
} 
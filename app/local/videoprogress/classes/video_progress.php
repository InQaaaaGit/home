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

/**
 * Class for handling video progress tracking
 */
class video_progress {
    /**
     * Update video progress for a user
     *
     * @param int $userid User ID
     * @param int $cmid Course module ID
     * @param string $videoid Video identifier
     * @param float $progress Progress percentage (0-100)
     * @return bool Success status
     */
    public static function update_progress($userid, $cmid, $videoid, $progress): bool
    {
        global $DB;

        $record = $DB->get_record('local_videoprogress', [
            'userid' => $userid,
            'cmid' => $cmid,
            'videoid' => $videoid
        ]);

        // Если запись существует и новый прогресс меньше или равен текущему - не обновляем
        if ($record && $progress <= $record->progress) {
            return true; // Возвращаем true, так как это не ошибка, просто нет необходимости в обновлении
        }

        $time = time();
        $data = [
            'userid' => $userid,
            'cmid' => $cmid,
            'videoid' => $videoid,
            'progress' => $progress,
            'timemodified' => $time
        ];

        if ($record) {
            $data['id'] = $record->id;
            return $DB->update_record('local_videoprogress', $data);
        } else {
            $data['timecreated'] = $time;
            return $DB->insert_record('local_videoprogress', $data) > 0;
        }
    }

    /**
     * Get video progress for a user
     *
     * @param int $userid User ID
     * @param int $cmid Course module ID
     * @param string $videoid Video identifier
     * @return float|null Progress percentage or null if not found
     */
    public static function get_progress($userid, $cmid, $videoid): ?float
    {
        global $DB;

        $record = $DB->get_record('local_videoprogress', [
            'userid' => $userid,
            'cmid' => $cmid,
            'videoid' => $videoid
        ]);

        return $record ? $record->progress : null;
    }

    /**
     * Get all video progress for a user in a course
     *
     * @param int $userid User ID
     * @param int $courseid Course ID
     * @return array Array of progress records
     */
    public static function get_course_progress($userid, $courseid): array
    {
        global $DB;

        $sql = "SELECT vp.*
                FROM {local_videoprogress} vp
                JOIN {course_modules} cm ON cm.id = vp.cmid
                WHERE vp.userid = :userid
                AND cm.course = :courseid";

        return $DB->get_records_sql($sql, [
            'userid' => $userid,
            'courseid' => $courseid
        ]);
    }

    /**
     * Update video segments and recalculate progress based on coverage
     *
     * @param int $userid User ID
     * @param int $cmid Course module ID
     * @param string $videoid Video identifier
     * @param array $segments Array of watched segments [[start, end], ...]
     * @param float $duration Total video duration in seconds
     * @return bool Success status
     */
    public static function update_segments($userid, $cmid, $videoid, array $segments, float $duration): bool
    {
        global $DB;

        // Получаем текущую запись
        $record = $DB->get_record('local_videoprogress', [
            'userid' => $userid,
            'cmid' => $cmid,
            'videoid' => $videoid
        ]);

        // Объединяем новые сегменты с существующими
        $existingSegments = [];
        if ($record && !empty($record->segments)) {
            $existingSegments = json_decode($record->segments, true) ?: [];
        }

        // Объединяем и оптимизируем сегменты
        $mergedSegments = self::merge_segments(array_merge($existingSegments, $segments));
        
        // Рассчитываем прогресс на основе покрытия
        $progress = self::calculate_coverage_progress($mergedSegments, $duration);

        // Если запись существует и новый прогресс меньше текущего - не обновляем
        if ($record && $progress < $record->progress) {
            return true; // Возвращаем true, так как это не ошибка
        }

        $time = time();
        $data = [
            'userid' => $userid,
            'cmid' => $cmid,
            'videoid' => $videoid,
            'segments' => json_encode($mergedSegments),
            'duration' => $duration,
            'progress' => $progress,
            'timemodified' => $time
        ];

        if ($record) {
            $data['id'] = $record->id;
            return $DB->update_record('local_videoprogress', $data);
        } else {
            $data['timecreated'] = $time;
            return $DB->insert_record('local_videoprogress', $data) > 0;
        }
    }

    /**
     * Merge overlapping segments and remove duplicates
     *
     * @param array $segments Array of segments [[start, end], ...]
     * @return array Merged segments
     */
    public static function merge_segments(array $segments): array
    {
        if (empty($segments)) {
            return [];
        }

        // Сортируем сегменты по начальному времени
        usort($segments, function($a, $b) {
            return $a[0] <=> $b[0];
        });

        $merged = [$segments[0]];

        for ($i = 1; $i < count($segments); $i++) {
            $last = &$merged[count($merged) - 1];
            $current = $segments[$i];

            // Если текущий сегмент перекрывается с последним объединенным
            if ($current[0] <= $last[1] + 0.1) { // Добавляем небольшую погрешность
                $last[1] = max($last[1], $current[1]);
            } else {
                $merged[] = $current;
            }
        }

        return $merged;
    }

    /**
     * Calculate progress percentage based on segments coverage
     *
     * @param array $segments Array of merged segments [[start, end], ...]
     * @param float $duration Total video duration
     * @return float Progress percentage (0-100)
     */
    public static function calculate_coverage_progress(array $segments, float $duration): float
    {
        if ($duration <= 0 || empty($segments)) {
            return 0.0;
        }

        $totalWatched = 0.0;
        foreach ($segments as $segment) {
            $totalWatched += max(0, $segment[1] - $segment[0]);
        }

        $progress = ($totalWatched / $duration) * 100;
        return min(100.0, round($progress, 2));
    }
} 
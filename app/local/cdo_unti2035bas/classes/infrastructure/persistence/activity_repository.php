<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use local_cdo_unti2035bas\domain\activity_entity;


class activity_repository {
    public function save(activity_entity $entity): activity_entity {
        $persistent = activity::from_domain($entity);
        $persistent->save();
        return $persistent->to_domain();
    }

    /**
     * @return array<activity_entity>
     */
    public function read_by_streamid(int $streamid): array {
        global $DB;
        $fields = activity::get_sql_fields('a', 'activity_');
        $sql = "SELECT {$fields}
                FROM {cdo_unti2035bas_activity} AS a
                JOIN {cdo_unti2035bas_theme} AS t ON t.id = a.themeid
                JOIN {cdo_unti2035bas_module} AS m ON m.id = t.moduleid
                JOIN {cdo_unti2035bas_block} AS b ON b.id = m.blockid
                WHERE b.streamid = :streamid
        ";
        $rows = $DB->get_records_sql($sql, ['streamid' => $streamid]);
        $persistents = array_map(
            fn($row) => new activity(0, activity::extract_record($row, 'activity_')),
            $rows,
        );
        return array_values(array_map(fn($p) => $p->to_domain(), $persistents));
    }

    /**
     * @return array<activity_entity>
     */
    public function read_by_blockid(int $blockid): array {
        global $DB;
        $fields = activity::get_sql_fields('a', 'activity_');
        $sql = "SELECT {$fields}
                FROM {cdo_unti2035bas_activity} AS a
                JOIN {cdo_unti2035bas_theme} AS t ON t.id = a.themeid
                JOIN {cdo_unti2035bas_module} AS m ON m.id = t.moduleid
                WHERE m.blockid = :blockid
        ";
        $rows = $DB->get_records_sql($sql, ['blockid' => $blockid]);
        $persistents = array_map(
            fn($row) => new activity(0, activity::extract_record($row, 'activity_')),
            $rows,
        );
        return array_values(array_map(fn($p) => $p->to_domain(), $persistents));
    }

    public function read(int $activityid): ?activity_entity {
        $persistent = activity::get_record(['id' => $activityid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    public function read_by_lrid(string $lrid): ?activity_entity {
        $persistent = activity::get_record(['lrid' => $lrid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    /**
     * @param int $themeid
     * @return array<activity_entity>
     */
    public function read_by_themeid(int $themeid): array {
        $persistents = activity::get_records(['themeid' => $themeid], 'position');
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }

    /**
     * Получает parent_course_id и flow_id по LRID activity
     * 
     * @param string $lrid LRID activity
     * @return array|null Массив с ключами 'parent_course_id' и 'flow_id' или null если не найдено
     */
    public function get_stream_params_by_activity_lrid(string $lrid): ?array {
        global $DB;
        
        $sql = "SELECT s.untiprogramid as parent_course_id, s.untiflowid as flow_id, 
                    s.courseid as course_id, s.groupid as group_id
                FROM {cdo_unti2035bas_activity} AS a
                JOIN {cdo_unti2035bas_theme} AS t ON t.id = a.themeid
                JOIN {cdo_unti2035bas_module} AS m ON m.id = t.moduleid
                JOIN {cdo_unti2035bas_block} AS b ON b.id = m.blockid
                JOIN {cdo_unti2035bas_stream} AS s ON s.id = b.streamid
                WHERE a.lrid = :lrid";
        
        $result = $DB->get_record_sql($sql, ['lrid' => $lrid]);
        
        return $result ? [
            'parent_course_id' => (int)$result->parent_course_id,
            'flow_id' => (int)$result->flow_id,
            'course_id' => (int)$result->course_id,
            'group_id' => (int)$result->group_id
        ] : null;
    }
}

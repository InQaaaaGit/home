<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use local_cdo_unti2035bas\domain\stream_entity;


class stream_repository {
    public function save(stream_entity $entity): stream_entity {
        $persistent = stream::from_domain($entity);
        $persistent->save();
        return $persistent->to_domain();
    }

    /**
     * @return array<stream_entity>
     */
    public function read_all(int $limit = 1000, int $offset = 0): array {
        $persistents = stream::get_records([], 'id', 'ASC', $offset, $limit);
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }

    public function read(int $streamid): ?stream_entity {
        $persistent = stream::get_record(['id' => $streamid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    public function read_by_lrid(string $lrid): ?stream_entity {
        $persistent = stream::get_record(['lrid' => $lrid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    public function count_all(): int {
        return stream::count_records();
    }

    public function exists_by_courseid(int $courseid): bool {
        return stream::record_exists_select('courseid = :courseid', ['courseid' => $courseid, 'deleted' => false]);
    }

    /**
     * @return array<stream_entity>
     */
    public function read_by_courseid(int $courseid): array {
        $persistents = stream::get_records(['courseid' => $courseid, 'deleted' => false]);
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }

    public function find_by_flow_id(int $flowId): ?stream_entity {
        $persistent = stream::get_record(['untiflowid' => $flowId]);
        return $persistent ? $persistent->to_domain() : null;
    }
}

<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use local_cdo_unti2035bas\domain\practice_diary_entity;


class practice_diary_repository {
    public function save(practice_diary_entity $entity): practice_diary_entity {
        $persistent = practice_diary::from_domain($entity);
        $persistent->save();
        return $persistent->to_domain();
    }

    public function read(int $practicediaryid): ?practice_diary_entity {
        $persistent = practice_diary::get_record(['id' => $practicediaryid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    /**
     * @return array<practice_diary_entity>
     */
    public function read_by_streamid(int $streamid): array {
        $persistents = practice_diary::get_records(['streamid' => $streamid]);
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }

    public function read_by_lrid(string $lrid): ?practice_diary_entity {
        $persistent = practice_diary::get_record(['lrid' => $lrid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    public function delete(int $practicediaryid): void {
        global $DB;
        $DB->delete_records(practice_diary::TABLE, ['id' => $practicediaryid]);
    }
}

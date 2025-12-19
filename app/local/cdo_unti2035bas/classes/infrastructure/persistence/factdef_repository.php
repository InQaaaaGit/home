<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use local_cdo_unti2035bas\domain\factdef_entity;


class factdef_repository {
    public function save(factdef_entity $entity): factdef_entity {
        $persistent = factdef::from_domain($entity);
        $persistent->save();
        return $persistent->to_domain();
    }

    public function read(int $factdefid): ?factdef_entity {
        $persistent = factdef::get_record(['id' => $factdefid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    public function read_by_lrid(string $lrid): ?factdef_entity {
        $persistent = factdef::get_record(['lrid' => $lrid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    /**
     * @param int $streamid
     * @return array<factdef_entity>
     */
    public function read_all_by_streamid(int $streamid): array {
        $persistents = factdef::get_records(['streamid' => $streamid]);
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }
}

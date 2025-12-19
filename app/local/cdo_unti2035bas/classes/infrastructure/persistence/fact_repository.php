<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use local_cdo_unti2035bas\domain\fact_entity;


class fact_repository {
    public function save(fact_entity $entity): fact_entity {
        $persistent = fact::from_domain($entity);
        $persistent->save();
        return $persistent->to_domain();
    }

    public function read(int $factid): ?fact_entity {
        $persistent = fact::get_record(['id' => $factid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    public function read_by_lrid(string $lrid): ?fact_entity {
        $persistent = fact::get_record(['lrid' => $lrid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    /**
     * @param int $factdefid
     * @param int $actoruntiid
     * @return array<fact_entity>
     */
    public function read_by_factdefid_actoruntiid(int $factdefid, int $actoruntiid): array {
        $persistents = fact::get_records(['factdefid' => $factdefid, 'actoruntiid' => $actoruntiid], 'id');
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }

    public function delete(int $factid): void {
        global $DB;
        $DB->delete_records(fact::TABLE, ['id' => $factid]);
    }
}

<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use local_cdo_unti2035bas\domain\theme_entity;


class theme_repository {
    public function save(theme_entity $entity): theme_entity {
        $persistent = theme::from_domain($entity);
        $persistent->save();
        return $persistent->to_domain();
    }

    public function read(int $themeid): ?theme_entity {
        $persistent = theme::get_record(['id' => $themeid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    public function read_by_lrid(string $lrid): ?theme_entity {
        $persistent = theme::get_record(['lrid' => $lrid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    /**
     * @return array<theme_entity>
     */
    public function read_by_moduleid(int $moduleid): array {
        $persistents = theme::get_records(['moduleid' => $moduleid], 'position');
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }

    /**
     * @return array<theme_entity>
     */
    public function read_by_sectionid(int $sectionid): array {
        $persistents = theme::get_records(['sectionid' => $sectionid]);
        return array_map(fn($p) => $p->to_domain(), $persistents);
    }
}

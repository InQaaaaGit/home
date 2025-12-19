<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use local_cdo_unti2035bas\domain\module_entity;


class module_repository {
    public function save(module_entity $entity): module_entity {
        $persistent = module::from_domain($entity);
        $persistent->save();
        return $persistent->to_domain();
    }

    public function read(int $moduleid): ?module_entity {
        $persistent = module::get_record(['id' => $moduleid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    public function read_by_lrid(string $lrid): ?module_entity {
        $persistent = module::get_record(['lrid' => $lrid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    /**
     * @return array<module_entity>
     */
    public function read_by_blockid(int $blockid): array {
        $persistents = module::get_records(['blockid' => $blockid], 'position');
        return array_map(fn($b) => $b->to_domain(), $persistents);
    }

    /**
     * @return array<module_entity>
     */
    public function read_by_sectionid(int $sectionid): array {
        $persistents = module::get_records(['sectionid' => $sectionid]);
        return array_map(fn($b) => $b->to_domain(), $persistents);
    }
}

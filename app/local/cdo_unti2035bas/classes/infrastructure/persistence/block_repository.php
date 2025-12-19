<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use local_cdo_unti2035bas\domain\block_entity;


class block_repository {
    public function save(block_entity $entity): block_entity {
        $persistent = block::from_domain($entity);
        $persistent->save();
        return $persistent->to_domain();
    }

    public function read(int $blockid): ?block_entity {
        $persistent = block::get_record(['id' => $blockid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    public function read_by_lrid(string $lrid): ?block_entity {
        $persistent = block::get_record(['lrid' => $lrid]);
        return $persistent ? $persistent->to_domain() : null;
    }

    /**
     * @return array<block_entity>
     */
    public function read_by_streamid(int $streamid): array {
        $persistents = block::get_records(['streamid' => $streamid], 'type_', 'DESC');
        return array_map(fn($b) => $b->to_domain(), $persistents);
    }

    /**
     * @return array<block_entity>
     */
    public function read_by_sectionid(int $sectionid): array {
        $persistents = block::get_records(['sectionid' => $sectionid]);
        return array_map(fn($b) => $b->to_domain(), $persistents);
    }
}

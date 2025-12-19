<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use core\persistent;
use local_cdo_unti2035bas\domain\block_entity;
use local_cdo_unti2035bas\domain\block_moodle_vo;
use local_cdo_unti2035bas\domain\override_vo;


class block extends persistent {
    const TABLE = 'cdo_unti2035bas_block';


    /**
     * @return array<string, array<string, mixed>>
     */
    protected static function define_properties(): array {
        return [
            'streamid' => [
                'type' => PARAM_INT,
            ],
            'type_' => [
                'type' => PARAM_RAW,
                'choices' => ['theoretical', 'practical'],
            ],
            'lrid' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'timestamp' => [
                'type' => PARAM_INT,
            ],
            'sectionid' => [
                'type' => PARAM_INT,
            ],
            'ismanual' => [
                'type' => PARAM_BOOL,
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'description' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'deleted' => [
                'type' => PARAM_BOOL,
            ],
            'changed' => [
                'type' => PARAM_BOOL,
            ],
            'version' => [
                'type' => PARAM_INT,
            ],
            'timesent' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
        ];
    }

    public static function from_domain(block_entity $entity): self {
        return new self(0, (object)array_filter([
            'id' => $entity->id,
            'streamid' => $entity->streamid,
            'type_' => $entity->type,
            'lrid' => $entity->lrid,
            'timestamp' => $entity->timestamp,
            'sectionid' => $entity->moodle->sectionid,
            'ismanual' => $entity->override->ismanual,
            'name' => $entity->override->name,
            'description' => $entity->override->description,
            'deleted' => $entity->deleted,
            'changed' => $entity->changed,
            'version' => $entity->version,
            'timesent' => $entity->timesent,
        ], fn($v) => !is_null($v)));
    }

    public function to_domain(): block_entity {
        return new block_entity(
            $this->get('id'),
            $this->get('streamid'),
            $this->get('type_'),
            $this->get('lrid'),
            $this->get('timestamp'),
            new block_moodle_vo($this->get('sectionid')),
            new override_vo(
                $this->get('ismanual'),
                $this->get('name'),
                $this->get('description'),
            ),
            $this->get('deleted'),
            $this->get('changed'),
            $this->get('version'),
            $this->get('timesent'),
        );
    }
}

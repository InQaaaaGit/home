<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use core\persistent;
use local_cdo_unti2035bas\domain\override_vo;
use local_cdo_unti2035bas\domain\theme_entity;
use local_cdo_unti2035bas\domain\theme_moodle_vo;
use local_cdo_unti2035bas\domain\theme_unti_vo;


class theme extends persistent {
    const TABLE = 'cdo_unti2035bas_theme';

    /**
     * @return array<string, array<string, mixed>>
     */
    protected static function define_properties(): array {
        return [
            'lrid' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'moduleid' => [
                'type' => PARAM_INT,
            ],
            'themeuntiid' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'timestamp' => [
                'type' => PARAM_INT,
            ],
            'sectionid' => [
                'type' => PARAM_INT,
            ],
            'position' => [
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

    public static function from_domain(theme_entity $entity): self {
        return new self(0, (object)array_filter([
            'id' => $entity->id,
            'lrid' => $entity->lrid,
            'moduleid' => $entity->moduleid,
            'sectionid' => $entity->moodle->sectionid,
            'position' => $entity->moodle->position,
            'themeuntiid' => $entity->unti->themeid,
            'timestamp' => $entity->timestamp,
            'ismanual' => $entity->override->ismanual,
            'name' => $entity->override->name,
            'description' => $entity->override->description,
            'deleted' => $entity->deleted,
            'changed' => $entity->changed,
            'version' => $entity->version,
            'timesent' => $entity->timesent,
        ], fn($v) => !is_null($v)));
    }

    public function to_domain(): theme_entity {
        return new theme_entity(
            $this->get('id'),
            $this->get('lrid'),
            $this->get('moduleid'),
            new theme_moodle_vo($this->get('sectionid'), $this->get('position')),
            new theme_unti_vo($this->get('themeuntiid')),
            $this->get('timestamp'),
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

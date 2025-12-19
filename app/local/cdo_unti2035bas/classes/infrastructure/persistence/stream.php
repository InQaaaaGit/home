<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use core\persistent;
use local_cdo_unti2035bas\domain\override_vo;
use local_cdo_unti2035bas\domain\stream_entity;
use local_cdo_unti2035bas\domain\stream_moodle_vo;
use local_cdo_unti2035bas\domain\stream_unti_vo;


class stream extends persistent {
    const TABLE = 'cdo_unti2035bas_stream';

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
            'timestamp' => [
                'type' => PARAM_INT,
            ],
            'courseid' => [
                'type' => PARAM_INT,
            ],
            'groupid' => [
                'type' => PARAM_INT,
            ],
            'sectionid' => [
                'type' => PARAM_INT,
            ],
            'untiuniqid' => [
                'type' => PARAM_RAW,
            ],
            'untiflowid' => [
                'type' => PARAM_INT,
            ],
            'untiprogramid' => [
                'type' => PARAM_INT,
            ],
            'untimethodistid' => [
                'type' => PARAM_INT,
            ],
            'academichourminutes' => [
                'type' => PARAM_INT,
            ],
            'isonline' => [
                'type' => PARAM_BOOL,
            ],
            'comment' => [
                'type' => PARAM_TEXT,
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
            'fdextensions' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'default' => '[]',
            ],
        ];
    }

    public static function from_domain(stream_entity $entity): self {
        return new self(0, (object)array_filter([
            'id' => $entity->id,
            'lrid' => $entity->lrid,
            'timestamp' => $entity->timestamp,
            'courseid' => $entity->moodle->courseid,
            'groupid' => $entity->moodle->groupid,
            'sectionid' => $entity->moodle->sectionid,
            'untiuniqid' => $entity->unti->uniqid,
            'untiflowid' => $entity->unti->flowid,
            'untiprogramid' => $entity->unti->programid,
            'untimethodistid' => $entity->unti->methodistid,
            'academichourminutes' => $entity->academichourminutes,
            'isonline' => $entity->isonline,
            'comment' => $entity->comment,
            'ismanual' => $entity->override->ismanual,
            'name' => $entity->override->name,
            'description' => $entity->override->description,
            'deleted' => $entity->deleted,
            'changed' => $entity->changed,
            'version' => $entity->version,
            'timesent' => $entity->timesent,
            'fdextensions' => json_encode($entity->fdextensions),
        ], fn($v) => !is_null($v)));
    }

    public function to_domain(): stream_entity {
        return new stream_entity(
            $this->get('id'),
            $this->get('lrid'),
            $this->get('timestamp'),
            new stream_moodle_vo(
                $this->get('courseid'),
                $this->get('groupid'),
                $this->get('sectionid'),
            ),
            new stream_unti_vo(
                $this->get('untiuniqid'),
                $this->get('untiprogramid'),
                $this->get('untiflowid'),
                $this->get('untimethodistid'),
            ),
            $this->get('academichourminutes'),
            $this->get('isonline'),
            $this->get('comment'),
            new override_vo(
                $this->get('ismanual'),
                $this->get('name'),
                $this->get('description'),
            ),
            $this->get('deleted'),
            $this->get('changed'),
            $this->get('version'),
            $this->get('timesent'),
            json_decode($this->get('fdextensions'), true),
        );
    }
}

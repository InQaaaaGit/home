<?php
namespace local_cdo_unti2035bas\infrastructure\persistence;

use core\persistent;
use local_cdo_unti2035bas\domain\log_record_vo;


class log_record extends persistent {
    const TABLE = 'cdo_unti2035bas_log';


    /**
     * @return array<string, array<string, mixed>>
     */
    protected static function define_properties(): array {
        return [
            'object_' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'objectid' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'objectversion' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'message' => [
                'type' => PARAM_TEXT,
            ],
            'timestamp' => [
                'type' => PARAM_INT,
            ],
            'level' => [
                'type' => PARAM_RAW,
                'choices' => ['debug', 'info', 'warning', 'error', 'critical'],
            ],
            'xapi' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
        ];
    }

    public static function from_domain(log_record_vo $vo): self {
        return new self(0, (object)array_filter([
            'object_' => $vo->object,
            'objectid' => $vo->objectid,
            'objectversion' => $vo->objectversion,
            'message' => $vo->message,
            'timestamp' => $vo->timestamp,
            'level' => $vo->level,
            'xapi' => $vo->xapi,
        ], fn($v) => !is_null($v)));
    }

    public function to_domain(): log_record_vo {
        return new log_record_vo(
            $this->get('object_'),
            $this->get('objectid'),
            $this->get('objectversion'),
            $this->get('message'),
            $this->get('timestamp'),
            $this->get('level'),
            $this->get('xapi'),
        );
    }
}

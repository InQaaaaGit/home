<?php
namespace local_cdo_unti2035bas\domain;


class log_record_vo {
    /** @readonly */
    public ?string $object;
    /** @readonly */
    public ?int $objectid;
    /** @readonly */
    public ?int $objectversion;
    /** @readonly */
    public int $timestamp;
    /** @readonly */
    public string $message;
    /** @readonly */
    public string $level;
    /** @readonly */
    public ?string $xapi;

    public function __construct(
        ?string $object,
        ?int $objectid,
        ?int $objectversion,
        string $message,
        int $timestamp,
        string $level,
        ?string $xapi
    ) {
        $this->object = $object;
        $this->objectid = $objectid;
        $this->objectversion = $objectversion;
        $this->message = $message;
        $this->timestamp = $timestamp;
        $this->level = $level;
        $this->xapi = $xapi;
    }
}

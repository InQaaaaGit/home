<?php
namespace local_cdo_unti2035bas\domain;


class stream_unti_vo {
    /** @readonly */
    public string $uniqid;
    /** @readonly */
    public int $programid;
    /** @readonly */
    public int $flowid;
    /** @readonly */
    public int $methodistid;

    public function __construct(?string $uniqid, int $programid, int $flowid, int $methodistid) {
        $this->uniqid = $uniqid ?: uniqid();
        $this->programid = $programid;
        $this->flowid = $flowid;
        $this->methodistid = $methodistid;
    }
}

<?php
namespace local_cdo_unti2035bas\domain;


class block_moodle_vo {
    /** @readonly */
    public int $sectionid;

    public function __construct(int $sectionid) {
        $this->sectionid = $sectionid;
    }
}

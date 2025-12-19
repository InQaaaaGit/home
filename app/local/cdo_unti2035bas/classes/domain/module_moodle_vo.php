<?php
namespace local_cdo_unti2035bas\domain;


class module_moodle_vo {
    /** @readonly */
    public int $sectionid;
    /** @readonly */
    public int $position;

    public function __construct(int $sectionid, int $position) {
        $this->sectionid = $sectionid;
        $this->position = $position;
    }
}

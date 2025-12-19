<?php
namespace local_cdo_unti2035bas\domain;


class activity_moodle_vo {
    /** @readonly */
    public int $modid;
    /** @readonly */
    public int $position;

    public function __construct(int $modid, int $position) {
        $this->modid = $modid;
        $this->position = $position;
    }
}

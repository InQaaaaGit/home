<?php
namespace local_cdo_unti2035bas\domain;


class assessment_moodle_vo {
    /** @readonly */
    public int $modid;

    public function __construct(int $modid) {
        $this->modid = $modid;
    }
}

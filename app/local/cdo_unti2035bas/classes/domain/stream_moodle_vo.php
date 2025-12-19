<?php
namespace local_cdo_unti2035bas\domain;


class stream_moodle_vo {
    /** @readonly */
    public int $courseid;
    /** @readonly */
    public int $groupid;
    /** @readonly */
    public int $sectionid;

    public function __construct(int $courseid, int $groupid, int $sectionid) {
        $this->courseid = $courseid;
        $this->groupid = $groupid;
        $this->sectionid = $sectionid;
    }
}

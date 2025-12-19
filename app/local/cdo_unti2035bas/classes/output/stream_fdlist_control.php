<?php
namespace local_cdo_unti2035bas\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;


class stream_fdlist_control implements renderable, templatable {
    private int $streamid;
    private string $tableid;

    public function __construct(int $streamid, string $tableid) {
        $this->streamid = $streamid;
        $this->tableid = $tableid;
    }

    /**
     * @return array<string, mixed>
     */
    public function export_for_template(renderer_base $output): array {
        return [
            'streamid' => $this->streamid,
            'tableid' => $this->tableid,
        ];
    }
}

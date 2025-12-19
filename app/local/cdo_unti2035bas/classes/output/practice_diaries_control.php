<?php
namespace local_cdo_unti2035bas\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;


class practice_diaries_control implements renderable, templatable {
    private string $uniqid;
    private int $streamid;
    private string $tableid;

    public function __construct(
        string $uniqid,
        int $streamid,
        string $tableid
    ) {
        $this->uniqid = $uniqid;
        $this->streamid = $streamid;
        $this->tableid = $tableid;
    }

    /**
     * @return array<string, mixed>
     */
    public function export_for_template(renderer_base $output): array {
        return [
            'uniqid' => $this->uniqid,
            'streamid' => $this->streamid,
            'tableid' => $this->tableid,
        ];
    }
}

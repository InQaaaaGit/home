<?php
namespace local_cdo_unti2035bas\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;


class fact_edit_control implements renderable, templatable {
    private int $factid;
    private string $tableid;

    public function __construct(int $factid, string $tableid) {
        $this->factid = $factid;
        $this->tableid = $tableid;
    }

    /**
     * @return array<string, mixed>
     */
    public function export_for_template(renderer_base $output): array {
        return [
            'factid' => $this->factid,
            'tableid' => $this->tableid,
        ];
    }
}

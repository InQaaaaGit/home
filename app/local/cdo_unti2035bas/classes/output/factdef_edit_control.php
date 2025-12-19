<?php
namespace local_cdo_unti2035bas\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;


class factdef_edit_control implements renderable, templatable {
    private int $factdefid;
    private string $tableid;

    public function __construct(int $factdefid, string $tableid) {
        $this->factdefid = $factdefid;
        $this->tableid = $tableid;
    }

    /**
     * @return array<string, mixed>
     */
    public function export_for_template(renderer_base $output): array {
        return [
            'factdefid' => $this->factdefid,
            'tableid' => $this->tableid,
        ];
    }
}

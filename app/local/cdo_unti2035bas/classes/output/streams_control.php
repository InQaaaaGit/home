<?php
namespace local_cdo_unti2035bas\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;


class streams_control implements renderable, templatable {
    private string $uniqid;
    private string $tableuniqid;

    public function __construct(string $tableuniqid) {
        $this->uniqid = uniqid();
        $this->tableuniqid = $tableuniqid;
    }

    /**
     * @return array<string, string>
     */
    public function export_for_template(renderer_base $output): array {
        return [
            'uniqid'           => $this->uniqid,
            'table_uniqid'     => $this->tableuniqid,
            'url_action'       => '',
        ];
    }
}

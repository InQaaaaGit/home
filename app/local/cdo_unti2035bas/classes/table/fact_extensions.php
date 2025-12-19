<?php
namespace local_cdo_unti2035bas\table;

use context;
use context_system;
use core_table\dynamic as dynamic_table;
use flexible_table;
use local_cdo_unti2035bas\domain\factdef_context_extension_vo;
use local_cdo_unti2035bas\domain\fact_entity;
use local_cdo_unti2035bas\domain\factdef_result_extension_vo;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;
use renderable;

defined('MOODLE_INTERNAL') || die();

/** @var \stdClass $CFG */
require_once("{$CFG->libdir}/tablelib.php");

class fact_extensions extends flexible_table implements dynamic_table, renderable, table_out_interface {
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);
        $this->is_downloadable(false);
        $this->sortable(false);
        $this->pageable(false);
    }

    public function guess_base_url(): void {
        $this->baseurl = new moodle_url('/local/cdo_unti2035bas/factdef_edit.php');
    }

    public function get_context(): context {
        return context_system::instance();
    }

    public function has_capability(): bool {
        return true;
    }

    public function out(?int $pagesize = null, ?bool $initialsbar = null): void {
        $this->pagesize = $pagesize ?: $this->get_default_per_page();
        $depends = new dependencies();
        $usecase = $depends->get_fact_extensions_list_use_case();
        $this->define_headers([
            get_string('title', 'local_cdo_unti2035bas'),
            get_string('name', 'local_cdo_unti2035bas'),
            get_string('type', 'local_cdo_unti2035bas'),
            get_string('value', 'local_cdo_unti2035bas'),
            get_string('actions', 'local_cdo_unti2035bas'),
        ]);
        $this->define_columns([
            'title',
            'name',
            'type',
            'value',
            'actions',
        ]);
        $rows = $usecase->execute((int)$this->uniqueid);
        $this->setup();
        foreach ($rows as $row) {
            $row['actions'] = $this->col_actions($row);
            $this->add_data_keyed($row);
        }
        $this->finish_output();
    }

    /**
     * @param array<string, mixed> $row
     */
    protected function col_actions(array $row): string {
        global $PAGE;
        $output = $PAGE->get_renderer('local_cdo_unti2035bas');
        $menu = new \action_menu();
        $delete = new \action_menu_link_primary(
            new \moodle_url('/'),
            new \pix_icon('t/delete', ''),
            get_string('delete'),
            [
                'data-action' => 'fact-extension-delete',
                'data-factid' => $row['factid'],
                'data-extensionname' => $row['name'],
            ],
        );
        $menu->add_primary_action($delete);
        return $output->render($menu);
    }
}

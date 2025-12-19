<?php
namespace local_cdo_unti2035bas\table;

use context;
use context_system;
use core_table\dynamic as dynamic_table;
use flexible_table;
use moodle_url;
use renderable;
use local_cdo_unti2035bas\ui\dependencies;
use local_cdo_unti2035bas\domain\fact_entity;

defined('MOODLE_INTERNAL') || die();

/** @var \stdClass $CFG */
require_once("{$CFG->libdir}/tablelib.php");


class facts extends flexible_table implements dynamic_table, renderable, table_out_interface {
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);
        $this->is_downloadable(false);
        $this->sortable(false);
        $this->pageable(false);
    }

    public function guess_base_url(): void {
        $this->baseurl = new moodle_url('/local/cdo_unti2035bas/facts.php');
    }

    public function get_context(): context {
        return context_system::instance();
    }

    public function has_capability(): bool {
        return true;
    }

    public function out(?int $pagesize = null, ?bool $initialsbar = null): void {
        $this->pagesize = $pagesize ?: $this->get_default_per_page();
        $this->define_headers([
            get_string('result', 'local_cdo_unti2035bas'),
            get_string('lrid', 'local_cdo_unti2035bas'),
            get_string('actions', 'local_cdo_unti2035bas'),
        ]);
        $this->define_columns([
            'result',
            'lrid',
            'actions',
        ]);
        if (!$this->filterset->has_filter('factdefid') || !$this->filterset->has_filter('actoruntiid')) {
            throw new \InvalidArgumentException();
        }
        /** @var int $factdefid */
        $factdefid = $this->filterset->get_filter('factdefid')->get_filter_values()[0];
        /** @var int $actoruntiid */
        $actoruntiid = $this->filterset->get_filter('actoruntiid')->get_filter_values()[0];
        $depends = new dependencies();
        $usecase = $depends->get_facts_read_use_case();
        $facts = $usecase->execute($factdefid, $actoruntiid);
        $this->setup();
        foreach ($facts as $fact) {
            $row = [
                'result' => json_encode((array)$fact->result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'lrid' => $fact->lrid ?: '<not send yet>',
                'actions' => $this->col_actions($fact),
            ];
            $this->add_data_keyed($row);
        }
        $this->finish_output();
    }

    protected function col_actions(fact_entity $fact): string {
        global $PAGE;
        $output = $PAGE->get_renderer('local_cdo_unti2035bas');
        $menu = new \action_menu();
        $log = new \action_menu_link_primary(
            new \moodle_url(
                '/local/cdo_unti2035bas/log.php',
                ['object_' => 'fact_entity', 'objectid' => $fact->id]
            ),
            new \pix_icon('log', '', 'local_cdo_unti2035bas'),
            get_string('log', 'local_cdo_unti2035bas'),
        );
        $menu->add_primary_action($log);
        $render = new \action_menu_link_primary(
            new \moodle_url(
                '/local/cdo_unti2035bas/render.php',
                ['mode' => 'fact', 'id' => $fact->id],
            ),
            new \pix_icon('t/print', ''),
            get_string('render', 'local_cdo_unti2035bas'),
        );
        $menu->add_primary_action($render);
        if ($fact->can_edit()) {
            $edit = new \action_menu_link_primary(
                new \moodle_url('/'),
                new \pix_icon('t/edit', ''),
                get_string('edit'),
                [
                    'data-action' => 'entity-edit',
                    'data-object_' => 'fact_entity',
                    'data-objectid' => $fact->id,
                ],
            );
            $menu->add_primary_action($edit);
            $fdextslist = new \action_menu_link_primary(
                new \moodle_url(
                    '/local/cdo_unti2035bas/fact_edit.php',
                    [
                        'streamid' => $fact->streamid,
                        'factdefid' => $fact->factdefid,
                        'factid' => $fact->id,
                        'actoruntiid' => $fact->actoruntiid,
                    ],
                ),
                new \pix_icon('fdedit', '', 'local_cdo_unti2035bas'),
                get_string('factdefedit', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($fdextslist);
        }
        if ($fact->can_send()) {
            $send = new \action_menu_link_primary(
                new \moodle_url('/'),
                new \pix_icon('t/go', ''),
                get_string('send', 'local_cdo_unti2035bas'),
                [
                    'data-action' => 'statement-send',
                    'data-url' => (new \moodle_url(
                        '/local/cdo_unti2035bas/send.php',
                        ['mode' => 'fact', 'id' => $fact->id],
                    ))->out(false),
                ],
            );
            $menu->add_primary_action($send);
        }
        if ($fact->can_delete()) {
            $delete = new \action_menu_link_primary(
                new \moodle_url('/'),
                new \pix_icon('t/delete', ''),
                get_string('delete'),
                [
                    'data-action' => 'fact-delete',
                    'data-factdefid' => $fact->factdefid,
                    'data-factid' => $fact->id,
                ],
            );
            $menu->add_primary_action($delete);
        }
        if ($fact->lrid) {
            $download = new \action_menu_link_primary(
                new \moodle_url('/local/cdo_unti2035bas/download.php', ['lrid' => $fact->lrid]),
                new \pix_icon('t/download', ''),
                get_string('download', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($download);
        }
        return $output->render($menu);
    }
}

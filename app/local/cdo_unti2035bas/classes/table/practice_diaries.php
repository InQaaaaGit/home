<?php
namespace local_cdo_unti2035bas\table;

use context;
use context_system;
use core_table\dynamic as dynamic_table;
use flexible_table;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;
use renderable;

defined('MOODLE_INTERNAL') || die();

/** @var \stdClass $CFG */
require_once("{$CFG->libdir}/tablelib.php");


class practice_diaries extends flexible_table implements dynamic_table, renderable, table_out_interface {
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);
        $this->is_downloadable(false);
        $this->sortable(false);
        $this->pageable(false);
    }

    public function guess_base_url(): void {
        $this->baseurl = new moodle_url('/local/cdo_unti2035bas/practice_diaries.php');
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
            get_string('student', 'local_cdo_unti2035bas'),
            get_string('lrid', 'local_cdo_unti2035bas'),
            get_string('actions', 'local_cdo_unti2035bas'),
        ]);
        $this->define_columns([
            'student',
            'lrid',
            'actions',
        ]);
        if (!$this->filterset->has_filter('streamid')) {
            throw new \InvalidArgumentException();
        }
        /** @var int $streamid */
        $streamid = $this->filterset->get_filter('streamid')->get_filter_values()[0];
        $depends = new dependencies();
        $usecase = $depends->get_practice_diaries_read_use_case();
        $rows = $usecase->execute($streamid);
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
        $diaryfile = new \action_menu_link_primary(
            new \moodle_url($row['diaryfileurl']),
            new \pix_icon('file', '', 'local_cdo_unti2035bas'),
            get_string('practicediaryfile', 'local_cdo_unti2035bas'),
        );
        $menu->add_primary_action($diaryfile);
        $render = new \action_menu_link_primary(
            new \moodle_url(
                '/local/cdo_unti2035bas/render.php',
                ['mode' => 'practice_diary', 'id' => $row['objectid']],
            ),
            new \pix_icon('t/print', ''),
            get_string('render', 'local_cdo_unti2035bas'),
        );
        if (!$row['lrid']) {
            $delete = new \action_menu_link_primary(
                new \moodle_url('/'),
                new \pix_icon('t/delete', ''),
                get_string('delete'),
                [
                    'data-action' => 'practice-diary-delete',
                    'data-practicediaryid' => $row['objectid'],
                ],
            );
            $menu->add_primary_action($delete);
            $send = new \action_menu_link_primary(
                new \moodle_url('/'),
                new \pix_icon('t/go', ''),
                get_string('send', 'local_cdo_unti2035bas'),
                [
                    'data-action' => 'statement-send',
                    'data-url' => (new \moodle_url(
                        '/local/cdo_unti2035bas/send.php',
                        ['mode' => 'practice_diary', 'id' => $row['objectid']],
                    ))->out(false),
                ],
            );
            $menu->add_primary_action($send);
        } else {
            $download = new \action_menu_link_primary(
                new \moodle_url('/local/cdo_unti2035bas/download.php', ['lrid' => $row['lrid']]),
                new \pix_icon('t/download', ''),
                get_string('download', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($download);
        }
        $menu->add_primary_action($render);
        return $output->render($menu);
    }
}

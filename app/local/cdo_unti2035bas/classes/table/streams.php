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

class streams extends flexible_table implements dynamic_table, renderable, table_out_interface {
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);
        $this->set_default_per_page(100);
        $this->is_downloadable(false);
        $this->sortable(false);
        $this->pageable(true);
    }

    public function guess_base_url(): void {
        $this->baseurl = new moodle_url('/local/cdo_unti2035bas/streams.php');
    }

    public function get_context(): context {
        return context_system::instance();
    }

    public function has_capability(): bool {
        return true;
    }

    public function out(?int $pagesize = null, ?bool $initialsbas = null): void {
        $this->pagesize = $pagesize ?: $this->get_default_per_page();
        $depends = new dependencies();
        $usecase = $depends->get_streams_read_use_case();
        $this->define_headers([
            get_string('timestamp', 'local_cdo_unti2035bas'),
            get_string('course'),
            get_string('group'),
            get_string('untiprogramid', 'local_cdo_unti2035bas'),
            get_string('untiflowid', 'local_cdo_unti2035bas'),
            get_string('untimethodistid', 'local_cdo_unti2035bas'),
            get_string('isonline', 'local_cdo_unti2035bas'),
            get_string('lrid', 'local_cdo_unti2035bas'),
            get_string('actions', 'local_cdo_unti2035bas'),
        ]);
        $this->define_columns([
            'timestamp_display',
            'course',
            'group',
            'program',
            'flow',
            'methodist',
            'isonline',
            'lrid',
            'actions',
        ]);
        /**
         * @var list<array<string, mixed>> $streams
         * @var int $total
         */
        [$streams, $total] = $usecase->execute($this->get_page_size(), $this->get_page_start());
        $this->totalrows = $total;
        $this->setup();
        foreach ($streams as $row) {
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
        $details = new \action_menu_link_primary(
            new \moodle_url('/local/cdo_unti2035bas/details.php', ['streamid' => $row['id']]),
            new \pix_icon('t/viewdetails', ''),
            get_string('details', 'local_cdo_unti2035bas'),
        );
        $menu->add_primary_action($details);
        $edit = new \action_menu_link_primary(
            new \moodle_url('/'),
            new \pix_icon('t/edit', ''),
            get_string('edit'),
            [
                'data-action' => 'entity-edit',
                'data-object_' => 'stream_entity',
                'data-objectid' => $row['id'],
            ],
        );
        $menu->add_primary_action($edit);
        $fdlist = new \action_menu_link_primary(
            new \moodle_url('/local/cdo_unti2035bas/stream_fdlist.php', ['streamid' => $row['id']]),
            new \pix_icon('fdedit', '', 'local_cdo_unti2035bas'),
            get_string('streamfdlist', 'local_cdo_unti2035bas'),
        );
        $menu->add_primary_action($fdlist);

        // Добавляем кнопку для управления просмотрами видео для потока
        if (!empty($row['flow'])) {
            $managevideo = new \action_menu_link_primary(
                new \moodle_url(
                    '/local/cdo_unti2035bas/pages/video_statement_management.php',
                    ['flow_id' => $row['flow']]
                ),
                new \pix_icon('t/play', ''),
                get_string('managevideo', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($managevideo);

            // Добавляем кнопку для отправки оценок потока
            $sendgrades = new \action_menu_link_primary(
                new \moodle_url(
                    '/local/cdo_unti2035bas/pages/send_grades.php',
                    ['flow_id' => $row['flow']]
                ),
                new \pix_icon('i/export', ''),
                get_string('sendgradesforstream', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($sendgrades);
        }

        return $output->render($menu);
    }
}

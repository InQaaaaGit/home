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

class details extends flexible_table implements dynamic_table, renderable, table_out_interface {
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);
        $this->is_downloadable(false);
        $this->sortable(false);
        $this->pageable(false);
    }

    public function guess_base_url(): void {
        $this->baseurl = new moodle_url('/local/cdo_unti2035bas/details.php');
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
        $usecase = $depends->get_stream_details_use_case();
        $this->define_headers([
            get_string('title', 'local_cdo_unti2035bas'),
            get_string('lrid', 'local_cdo_unti2035bas'),
            get_string('hours', 'local_cdo_unti2035bas'),
            get_string('actions', 'local_cdo_unti2035bas'),
        ]);
        $this->define_columns([
            'title_display',
            'lrid',
            'hours',
            'actions',
        ]);
        $rows = $usecase->execute((int)$this->uniqueid);
        $this->setup();
        foreach ($rows as $row) {
            $classname = $row['level'] . '_level';
            if ($row['deleted']) {
                $classname .= ' entity_deleted';
            }
            if ($row['object_'] == 'stream_entity') {
                $row['title_display'] = '<i>' . get_string('flow', 'local_cdo_unti2035bas') . '</i>'
                    . ": {$row['title']}";
            } else if ($row['object_'] == 'block_entity') {
                $row['title_display'] = '<i>' . get_string('block', 'local_cdo_unti2035bas') . "</i>: {$row['title']}";
            } else if ($row['object_'] == 'module_entity') {
                $row['title_display'] = '<i>' . get_string('module', 'local_cdo_unti2035bas') . "</i>: {$row['title']}";
            } else if ($row['object_'] == 'theme_entity') {
                $row['title_display'] = '<i>' . get_string('theme', 'local_cdo_unti2035bas') . "</i>: {$row['title']}";
            } else if ($row['object_'] == 'activity_entity') {
                $row['title_display'] = $row['title'];
            } else if ($row['object_'] == 'assessment_entity') {
                $row['title_display'] = '<i>' . get_string('assessment', 'local_cdo_unti2035bas') . "</i>: {$row['title']}";
            } else if ($row['object_'] == 'factdef_entity') {
                $row['title_display'] = "<i>FD</i>: {$row['title']}";
            }
            $row['actions'] = $this->col_actions($row);
            if (isset($row['lectureshours']) || isset($row['workshopshours']) || isset($row['independentworkhours'])) {
                $row['hours'] = join(
                    ' + ',
                    [$row['lectureshours'] ?? 0, $row['workshopshours'] ?? 0, $row['independentworkhours'] ?? 0],
                );
            }
            $this->add_data_keyed($row, $classname);
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
        $mode = str_replace('_entity', '', $row['object_']);
        $log = new \action_menu_link_primary(
            new \moodle_url(
                '/local/cdo_unti2035bas/log.php',
                ['object_' => $row['object_'], 'objectid' => $row['objectid']]
            ),
            new \pix_icon('log', '', 'local_cdo_unti2035bas'),
            get_string('log', 'local_cdo_unti2035bas'),
        );
        $menu->add_primary_action($log);
        $render = new \action_menu_link_primary(
            new \moodle_url(
                '/local/cdo_unti2035bas/render.php',
                ['mode' => $mode, 'id' => $row['objectid']],
            ),
            new \pix_icon('t/print', ''),
            get_string('render', 'local_cdo_unti2035bas'),
        );
        $menu->add_primary_action($render);
        if ($row['changed']) {
            $send = new \action_menu_link_primary(
                new \moodle_url('/'),
                new \pix_icon('t/go', ''),
                get_string('send', 'local_cdo_unti2035bas'),
                [
                    'data-action' => 'statement-send',
                    'data-url' => (new \moodle_url(
                        '/local/cdo_unti2035bas/send.php',
                        ['mode' => $mode, 'id' => $row['objectid']],
                    ))->out(false),
                ],
            );
            $menu->add_primary_action($send);
        }
        if ($row['lrid']) {
            $cancel = new \action_menu_link_primary(
                new \moodle_url('/'),
                new \pix_icon('t/stop', ''),
                get_string('cancel', 'local_cdo_unti2035bas'),
                [
                    'data-action' => 'statement-cancel',
                    'data-lrid' => $row['lrid'],
                    'data-url' => (new \moodle_url(
                        '/local/cdo_unti2035bas/cancel.php',
                        [
                            'lrid' => $row['lrid'],
                            'streamid' => $row['streamid'],
                            'object_' => $row['object_'],
                            'objectid' => $row['objectid'],
                        ],
                    ))->out(false),
                ],
            );
            $menu->add_primary_action($cancel);
        }
        if (
            in_array(
                $row['object_'],
                [
                    'stream_entity',
                    'module_entity',
                    'theme_entity',
                    'activity_entity',
                    'assessment_entity',
                    'factdef_entity',
                ],
            )
        ) {
            $edit = new \action_menu_link_primary(
                new \moodle_url('/'),
                new \pix_icon('t/edit', ''),
                get_string('edit'),
                [
                    'data-action' => 'entity-edit',
                    'data-object_' => $row['object_'],
                    'data-objectid' => $row['objectid'],
                ],
            );
            $menu->add_primary_action($edit);
        }
        if ($row['object_'] == 'stream_entity') {
            $sync = new \action_menu_link_primary(
                new \moodle_url('/'),
                new \pix_icon('t/reload', ''),
                get_string('sync', 'local_cdo_unti2035bas'),
                [
                    'data-action' => 'stream-sync',
                    'data-object_' => $row['object_'],
                    'data-objectid' => $row['objectid'],
                ],
            );
            $menu->add_primary_action($sync);
            $fdlist = new \action_menu_link_primary(
                new \moodle_url('/local/cdo_unti2035bas/stream_fdlist.php', ['streamid' => $row['objectid']]),
                new \pix_icon('fdedit', '', 'local_cdo_unti2035bas'),
                get_string('streamfdlist', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($fdlist);
            $practicediaries = new \action_menu_link_primary(
                new \moodle_url('/local/cdo_unti2035bas/practice_diaries.php', ['streamid' => $row['objectid']]),
                new \pix_icon('practicediaries', '', 'local_cdo_unti2035bas'),
                get_string('practicediaries', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($practicediaries);
        }
        if ($row['lrid']) {
            $download = new \action_menu_link_primary(
                new \moodle_url('/local/cdo_unti2035bas/download.php', ['lrid' => $row['lrid']]),
                new \pix_icon('t/download', ''),
                get_string('download', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($download);
        }

        // Добавляем новое действие для выполнения скрипта только для активностей в практических блоках
        if ($row['object_'] == 'activity_entity' && isset($row['type_']) && $row['type_'] == 'practical' && $row['lrid']) {
            $execute = new \action_menu_link_primary(
                new \moodle_url(
                    '/local/cdo_unti2035bas/send_practice.php',
                    ['lrid' => $row['lrid']]
                ),
                new \pix_icon('t/right', ''),
                get_string('execute', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($execute);
        }

        // Добавляем кнопку для отправки активности в UNTI только для активностей с "самоподготовка" в названии
        if (
            $row['object_'] == 'activity_entity' && $row['lrid'] &&
            isset($row['title']) && mb_stripos($row['title'], 'самоподготовка') !== false
        ) {
            $sendactivity = new \action_menu_link_primary(
                new \moodle_url(
                    '/local/cdo_unti2035bas/send_activity.php',
                    ['lrid' => $row['lrid']]
                ),
                new \pix_icon('t/up', ''),
                get_string('sendactivity', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($sendactivity);
        }
        if ($row['object_'] == 'factdef_entity') {
            $fdextslist = new \action_menu_link_primary(
                new \moodle_url(
                    '/local/cdo_unti2035bas/factdef_edit.php',
                    ['streamid' => $row['streamid'], 'factdefid' => $row['objectid']]
                ),
                new \pix_icon('fdedit', '', 'local_cdo_unti2035bas'),
                get_string('factdefedit', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($fdextslist);
            $factslist = new \action_menu_link_primary(
                new \moodle_url(
                    '/local/cdo_unti2035bas/facts.php',
                    ['factdefid' => $row['objectid'], 'streamid' => $row['streamid']],
                ),
                new \pix_icon('actorfacts', '', 'local_cdo_unti2035bas'),
                get_string('streamfacts', 'local_cdo_unti2035bas'),
            );
            $menu->add_primary_action($factslist);
        }

        return $output->render($menu);
    }
}

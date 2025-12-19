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

class log extends flexible_table implements dynamic_table, renderable, table_out_interface {
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);
        $this->set_default_per_page(100);
        $this->is_downloadable(false);
        $this->sortable(false);
        $this->pageable(true);
    }

    public function guess_base_url(): void {
        $this->baseurl = new moodle_url('/local/cdo_unti2035bas/log.php');
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
        $usecase = $depends->get_log_read_use_case();
        $this->define_headers([
            get_string('timestamp', 'local_cdo_unti2035bas'),
            get_string('level', 'local_cdo_unti2035bas'),
            get_string('message', 'local_cdo_unti2035bas'),
            get_string('object_', 'local_cdo_unti2035bas'),
            get_string('objectid', 'local_cdo_unti2035bas'),
            get_string('objectversion', 'local_cdo_unti2035bas'),
        ]);
        $this->define_columns([
            'timestamp_display',
            'level',
            'message',
            'object_',
            'objectid',
            'objectversion',
        ]);
        $filter = [];
        if ($this->filterset->has_filter('object_')) {
            $filterobject = $this->filterset->get_filter('object_');
            $filter['object_'] = $filterobject->get_filter_values()[0];
        }
        if ($this->filterset->has_filter('objectid')) {
            $filterobjectid = $this->filterset->get_filter('objectid');
            $filter['objectid'] = $filterobjectid->get_filter_values()[0];
        }
        /**
         * @var list<array<string, mixed>> $records
         * @var int $total
         */
        [$records, $total] = $usecase->execute(
            $this->get_page_size(),
            $this->get_page_start(),
            $filter,
        );
        $this->totalrows = $total;
        $this->setup();
        foreach ($records as $record) {
            $this->add_data_keyed($record);
        }
        $this->finish_output();
    }
}

<?php
namespace local_cdo_unti2035bas;

use context_system;
use local_cdo_unti2035bas\output\stream_fdlist_control;
use local_cdo_unti2035bas\table\stream_fdlist as table;
use local_cdo_unti2035bas\table\stream_fdlist_filterset as filterset;
use moodle_url;

require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();

require_admin();
$context = context_system::instance();
/** @var int */
$streamid = required_param('streamid', PARAM_INT);

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/stream_fdlist.php'));
$title = get_string('streamfdlist', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$table = new table((string)$streamid);
$table->set_filterset(new filterset());
$control = new stream_fdlist_control($streamid, $table->uniqueid);

/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
echo $output->render($control);
echo $output->render($table);
echo $output->footer();

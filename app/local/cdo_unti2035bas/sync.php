<?php
namespace local_cdo_unti2035bas;

use context_system;
use local_cdo_unti2035bas\ui\dependencies;
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
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/sync.php'));
$title = 'SYNC';
$PAGE->set_title($title);
$PAGE->set_heading($title);

/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
$depends = new dependencies();
$usecase = $depends->get_stream_sync_use_case();
$usecase->execute($streamid);
echo $output->footer();

<?php
namespace local_cdo_unti2035bas;

use context_system;
use local_cdo_unti2035bas\output\streams_control;
use local_cdo_unti2035bas\table\streams as table;
use local_cdo_unti2035bas\table\streams_filterset as filterset;
use moodle_url;


require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();

require_admin();
$context = context_system::instance();

$table = new table(uniqid());
$table->set_filterset(new filterset());

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$widget = new streams_control($table->uniqueid);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/streams.php'));
$title = get_string('streamstitle', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$PAGE->requires->js_call_amd('local_cdo_unti2035bas/actions_control', 'init', [$table->uniqueid]);

/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
echo $output->render($widget);
echo $output->render($table);
echo $output->footer();

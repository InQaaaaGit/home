<?php
namespace local_cdo_unti2035bas;

use context_system;
use core_table\local\filter\filter;
use core_table\local\filter\integer_filter;
use core_table\local\filter\string_filter;
use local_cdo_unti2035bas\table\log as table;
use local_cdo_unti2035bas\table\log_filterset as filterset;
use moodle_url;

require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();


require_admin();
$context = context_system::instance();
/** @var string */
$object = required_param('object_', PARAM_RAW);
/** @var int */
$objectid = required_param('objectid', PARAM_INT);

$table = new table(uniqid());
$filterset = new filterset();
$filterset->add_filter(new string_filter('object_', filter::JOINTYPE_ANY, [$object]));
$filterset->add_filter(new integer_filter('objectid', filter::JOINTYPE_ANY, [$objectid]));
$table->set_filterset($filterset);

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/log.php'));
$title = get_string('log', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
echo $output->render($table);
echo $output->footer();

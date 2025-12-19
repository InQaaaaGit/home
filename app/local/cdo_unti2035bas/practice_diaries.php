<?php
namespace local_cdo_unti2035bas;


use context_system;
use core_table\local\filter\filter;
use core_table\local\filter\integer_filter;
use local_cdo_unti2035bas\output\practice_diaries_control;
use local_cdo_unti2035bas\table\practice_diaries as table;
use local_cdo_unti2035bas\table\practice_diaries_filterset as filterset;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;


require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();

require_admin();
$context = context_system::instance();
/** @var int */
$streamid = required_param('streamid', PARAM_INT);

$depends = new dependencies();

$table = new table(uniqid());
$widget = new practice_diaries_control(uniqid(), $streamid, $table->uniqueid);
$filterset = new filterset();
$filterset->add_filter(new integer_filter('streamid', filter::JOINTYPE_ANY, [$streamid]));
$table->set_filterset($filterset);

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/practice_diaries.php'));
$title = get_string('practicediariestitle', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add(
    get_string('streamstitle', 'local_cdo_unti2035bas'),
    new \moodle_url('/local/cdo_unti2035bas/streams.php'),
);
$PAGE->navbar->add(
    get_string('details', 'local_cdo_unti2035bas'),
    new \moodle_url('/local/cdo_unti2035bas/details.php', ['streamid' => $streamid]),
);
$PAGE->navbar->add(
    get_string('practicediariestitle', 'local_cdo_unti2035bas'),
    new \moodle_url('/local/cdo_unti2035bas/practice_diaries.php', ['streamid' => $streamid]),
);


/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
echo $output->render($widget);
echo $output->render($table);
echo $output->footer();

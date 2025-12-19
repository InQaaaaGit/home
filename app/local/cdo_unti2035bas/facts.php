<?php
namespace local_cdo_unti2035bas;


use context_system;
use core_table\local\filter\filter;
use core_table\local\filter\integer_filter;
use local_cdo_unti2035bas\output\facts_control;
use local_cdo_unti2035bas\table\facts as table;
use local_cdo_unti2035bas\table\facts_filterset as filterset;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;


require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();

require_admin();
$context = context_system::instance();
/** @var int */
$factdefid = required_param('factdefid', PARAM_INT);
/** @var int */
$streamid = required_param('streamid', PARAM_INT);
/** @var ?int */
$actoruntiid = optional_param('actoruntiid', null, PARAM_INT);

$depends = new dependencies();

$table = new table(uniqid());
$widget = new facts_control($factdefid, $actoruntiid, $table->uniqueid, $depends);
$filterset = new filterset();
$filterset->add_filter(new integer_filter('factdefid', filter::JOINTYPE_ANY, [$factdefid]));
if ($widget->initialactoruntiid) {
    $filterset->add_filter(new integer_filter('actoruntiid', filter::JOINTYPE_ANY, [$widget->initialactoruntiid]));
}
/** @var ?array<string, mixed> $student */
$student = $widget->filterdata['students'][0] ?? null;
$table->set_filterset($filterset);

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/facts.php'));
$title = get_string('factstitle', 'local_cdo_unti2035bas');
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
    get_string('factstitle', 'local_cdo_unti2035bas'),
    new \moodle_url('/local/cdo_unti2035bas/facts.php', array_filter([
        'streamid' => $streamid, 'factdefid' => $factdefid, 'actoruntiid' => $widget->initialactoruntiid,
    ])),
);


/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
echo $output->render($widget);
echo $output->render($table);
echo $output->footer();

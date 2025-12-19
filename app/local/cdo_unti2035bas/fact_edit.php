<?php
namespace local_cdo_unti2035bas;

use context_system;
use local_cdo_unti2035bas\output\fact_edit_control;
use local_cdo_unti2035bas\table\fact_extensions as table;
use local_cdo_unti2035bas\table\fact_extensions_filterset as filterset;
use moodle_url;

require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();


require_admin();
$context = context_system::instance();
/** @var int */
$streamid = required_param('streamid', PARAM_INT);
/** @var int */
$factdefid = required_param('factdefid', PARAM_INT);
/** @var int */
$factid = required_param('factid', PARAM_INT);
/** @var int */
$actoruntiid = required_param('actoruntiid', PARAM_INT);

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/fact_edit.php'));
$title = get_string('factedit', 'local_cdo_unti2035bas');
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
    new \moodle_url(
        '/local/cdo_unti2035bas/facts.php',
        ['streamid' => $streamid, 'factdefid' => $factdefid, 'actoruntiid' => $actoruntiid]
    ),
);
$PAGE->navbar->add(
    get_string('factedittitle', 'local_cdo_unti2035bas'),
    new \moodle_url(
        '/local/cdo_unti2035bas/fact_edit.php',
        ['streamid' => $streamid, 'factdefid' => $factdefid, 'factid' => $factid, 'actoruntiid' => $actoruntiid]
    ),
);

$table = new table((string)$factid);
$table->set_filterset(new filterset());
$control = new fact_edit_control($factid, $table->uniqueid);

/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
echo $output->render($control);
echo $output->render($table);
echo $output->footer();

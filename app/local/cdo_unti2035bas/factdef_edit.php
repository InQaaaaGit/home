<?php
namespace local_cdo_unti2035bas;

use context_system;
use local_cdo_unti2035bas\output\factdef_edit_control;
use local_cdo_unti2035bas\table\factdef_extensions as table;
use local_cdo_unti2035bas\table\factdef_extensions_filterset as filterset;
use moodle_url;

require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();


require_admin();
$context = context_system::instance();
/** @var int */
$streamid = required_param('streamid', PARAM_INT);
/** @var int */
$factdefid = required_param('factdefid', PARAM_INT);

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/factdef_edit.php'));
$title = get_string('factdefedit', 'local_cdo_unti2035bas');
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
    get_string('factdeftitle', 'local_cdo_unti2035bas'),
    new \moodle_url(
        '/local/cdo_unti2035bas/factdef_edit.php',
        ['streamid' => $streamid, 'factdefid' => $factdefid]
    ),
);

$table = new table((string)$factdefid);
$table->set_filterset(new filterset());
$control = new factdef_edit_control($factdefid, $table->uniqueid);

/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
echo $output->render($control);
echo $output->render($table);
echo $output->footer();

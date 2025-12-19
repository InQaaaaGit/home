<?php
namespace local_cdo_unti2035bas;

use context_system;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;


/**
 * @var \stdClass $CFG
 */

require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();

require_admin();
$context = context_system::instance();
/** @var string $lrid */
$lrid = required_param('lrid', PARAM_TEXT);

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/download.php'));
$title = get_string('download', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$depends = new dependencies();
$usecase = $depends->get_statement_download_use_case();

/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
echo "<pre>";
echo $usecase->execute($lrid);
echo "</pre>";
echo $output->footer();

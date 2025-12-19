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
/** @var int $streamid */
$streamid = required_param('streamid', PARAM_INT);
/** @var string $object */
$object = required_param('object_', PARAM_TEXT);
/** @var int $objectid */
$objectid = required_param('objectid', PARAM_INT);
/** @var string $lrid */
$lrid = required_param('lrid', PARAM_TEXT);

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/cancel.php'));
$title = get_string('cancel', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$depends = new dependencies();
$usecase = $depends->get_statement_cancel_use_case();
$usecase->execute($streamid, $object, $objectid, $lrid);

$url = get_local_referer(false) ?: ($CFG->wwwroot . '/');
redirect(new \moodle_url($url), get_string('cancelok', 'local_cdo_unti2035bas'));

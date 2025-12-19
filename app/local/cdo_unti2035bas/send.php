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
/** @var int $id */
$id = required_param('id', PARAM_INT);
$mode = required_param('mode', PARAM_RAW);

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/send.php'));
$title = get_string('send', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$depends = new dependencies();
if ($mode == 'stream') {
    $usecase = $depends->get_stream_send_use_case();
} else if ($mode == 'block') {
    $usecase = $depends->get_block_send_use_case();
} else if ($mode == 'module') {
    $usecase = $depends->get_module_send_use_case();
} else if ($mode == 'theme') {
    $usecase = $depends->get_theme_send_use_case();
} else if ($mode == 'activity') {
    $usecase = $depends->get_activity_send_use_case();
} else if ($mode == 'assessment') {
    $usecase = $depends->get_assessment_send_use_case();
} else if ($mode == 'factdef') {
    $usecase = $depends->get_factdef_send_use_case();
} else if ($mode == 'fact') {
    $usecase = $depends->get_fact_send_use_case();
} else if ($mode == 'practice_diary') {
    $usecase = $depends->get_practice_diary_send_use_case();
}
assert(isset($usecase));
$usecase->execute($id);
$url = get_local_referer(false) ?: ($CFG->wwwroot . '/');
redirect(new \moodle_url($url), get_string('sendok', 'local_cdo_unti2035bas'));

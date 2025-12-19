<?php
namespace local_cdo_unti2035bas;

use context_system;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;

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
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/render.php'));
$title = get_string('render', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$depends = new dependencies();
if ($mode == 'stream') {
    $usecase = $depends->get_stream_render_use_case();
} else if ($mode == 'block') {
    $usecase = $depends->get_block_render_use_case();
} else if ($mode == 'module') {
    $usecase = $depends->get_module_render_use_case();
} else if ($mode == 'theme') {
    $usecase = $depends->get_theme_render_use_case();
} else if ($mode == 'activity') {
    $usecase = $depends->get_activity_render_use_case();
} else if ($mode == 'assessment') {
    $usecase = $depends->get_assessment_render_use_case();
} else if ($mode == 'factdef') {
    $usecase = $depends->get_factdef_render_use_case();
} else if ($mode == 'fact') {
    $usecase = $depends->get_fact_render_use_case();
} else if ($mode == 'practice_diary') {
    $usecase = $depends->get_practice_diary_render_use_case();
}
assert(isset($usecase));

/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
echo "<pre>";
echo $usecase->execute($id);
echo "</pre>";
echo $output->footer();

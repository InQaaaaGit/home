<?php
namespace local_cdo_unti2035bas;

use context_system;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;

require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();

require_admin();
/** @var int $streamid */
$streamid = required_param('streamid', PARAM_INT);
/** @var string $blocklrid */
$blocklrid = optional_param('blocklrid', '', PARAM_RAW);
$dry = (bool)optional_param('dry', 1, PARAM_BOOL);
/** @var string $actor */
$actor = required_param('actor', PARAM_RAW);
/** @var string $s3url */
$s3url = required_param('s3url', PARAM_RAW);
/** @var string $mime */
$mime = required_param('mime', PARAM_RAW);
/** @var int $filesize */
$filesize = required_param('filesize', PARAM_INT);
/** @var string $sha256 */
$sha256 = required_param('sha256', PARAM_RAW);

$context = context_system::instance();

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/send_diary.php'));
$title = 'Send diary';
$PAGE->set_title($title);
$PAGE->set_heading($title);

$depends = new dependencies();
$stream_repo = $depends->get_stream_repo();
$block_repo = $depends->get_block_repo();
$stream = $stream_repo->read($streamid);
if (!$stream) {
    throw new \Exception("stream not found");
}
if ($blocklrid) {
    $block = $block_repo->read_by_lrid($blocklrid);
    if (!$block) {
        throw new \Exception("block not found");
    }
} else {
    $blocks = $block_repo->read_by_streamid($streamid);
    $blocks_practice = array_values(
        array_filter($blocks, fn($b) => $b->type == 'practical' && $b->deleted == false)
    );
    if (count($blocks_practice) != 1) {
        throw new \Exception("practice block not found");
    }
    $block = $blocks_practice[0];
}
if (!$block->lrid) {
    throw new \Exception("lrid not present in block");
}
$xapi_service = $depends->get_practice_diary_xapi_service();
$request = $xapi_service->execute(
    $stream,
    $block,
    $actor,
    $s3url,
    $mime,
    $filesize,
    $sha256,
);
$data = $request->dump();

/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
echo '<pre>', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '', '</pre>';
if (!$dry) {
    $client = $depends->get_xapi_client();
    $res = $client->send([$request]);
    echo '<pre>';
    print_r($res);
    echo '</pre>';
}
echo $output->footer();

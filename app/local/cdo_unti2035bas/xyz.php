<?php
namespace local_cdo_unti2035bas;

use context_system;
use DateTime;
use local_cdo_unti2035bas\infrastructure\xapi\builders\cancel_statement;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;

require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();

$statementlrid = required_param('statementlrid', PARAM_RAW);
$objectid = required_param('objectid', PARAM_RAW);
$dry = (bool)optional_param('dry', 1, PARAM_BOOL);

$context = context_system::instance();

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/xyz.php'));
$title = 'XYZ';
$PAGE->set_title($title);
$PAGE->set_heading($title);

$builder = new cancel_statement();
$builder->with_timestamp(new DateTime());
$builder->with_prefix('https://sdo.webinar.kadastr.ru');
$builder->with_actorname('2018785');
$builder->with_statementlrid($statementlrid);
$builder->with_objectid($objectid);
$builder->with_unticourseid(29413);
$builder->with_untiflowid(9244);
$request = $builder->build();
$data = $request->dump();


$depends = new dependencies();
$client = $depends->get_xapi_client();

/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();
echo '<pre>', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '', '</pre>';
if (!$dry) {
    $res = $client->send([$request]);
    echo '<pre>';
    print_r($res);
    echo '</pre>';
}
echo $output->footer();

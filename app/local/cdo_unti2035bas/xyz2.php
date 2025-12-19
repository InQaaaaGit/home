<?php
namespace local_cdo_unti2035bas;

use context_system;
use DateTime;
use local_cdo_unti2035bas\infrastructure\xapi\builders\cancel_statement;
use local_cdo_unti2035bas\ui\dependencies;
use moodle_url;

require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();

$dry = (bool)optional_param('dry', 1, PARAM_BOOL);

$context = context_system::instance();

/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/xyz.php'));
$title = 'XYZ';
$PAGE->set_title($title);
$PAGE->set_heading($title);



/** @var \core_renderer $output */
$output = $PAGE->get_renderer('local_cdo_unti2035bas');
echo $output->header();

echo '<pre>';
$depends = new dependencies();
$client = $depends->get_xapi_client();
$resmap = [];
$data = json_decode(file_get_contents("/app/test4.json"), true);
foreach ($data as $key => $value) {
    $builder = new cancel_statement();
    $builder->with_timestamp(new DateTime());
    $builder->with_prefix('https://sdo.webinar.kadastr.ru');
    $builder->with_actorname('2018785');
    $builder->with_statementlrid($key);
    $builder->with_objectid($value);
    $builder->with_unticourseid(29413);
    $builder->with_untiflowid(9244);
    $request = $builder->build();
    $res = $client->send([$request]);
    print_r([$key, $value, $res]);
    $resmap[$key] = $res[0];
}
file_put_contents('/tmp2/php/del9244_1.txt', json_encode($resmap));
echo '</pre>';

echo $output->footer();

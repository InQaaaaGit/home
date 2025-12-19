<?php
namespace local_cdo_unti2035bas;

use local_cdo_unti2035bas\ui\dependencies;

/**
 * @var \stdClass $CFG
 */

require_once('../../config.php');
require_once("{$CFG->libdir}/adminlib.php");

admin_externalpage_setup('testxapiconf');

$depends = new dependencies();
$usecase = $depends->get_s3_test_use_case();
$usecase->execute();
$url = get_local_referer(false) ?: ($CFG->wwwroot . '/');
redirect(new \moodle_url($url), get_string('s3testok', 'local_cdo_unti2035bas'));

<?php


use ConvertApi\ConvertApi;
use tool_imageoptimize\tool_image_optimize_helper;

require_once(__DIR__ . "/../../config.php");
$imageoptimizehelper = tool_image_optimize_helper::get_instance();
$imageoptimizehelper->task_call_populate_imageoptimze_table();
$imageoptimizehelper = tool_image_optimize_helper::get_instance();
$imageoptimizehelper->task_call_optimization();
/*require_once("vendor/convertapi/convertapi-php/lib/ConvertApi/autoload.php");

ConvertApi::setApiCredentials('secret_P6RpeCk5eqmuqnG7');
$result = ConvertApi::convert(
    'compress',
    [
        'File' => 'files/tt.pdf'
    ],
    'pdf'
);
$result->saveFiles('files/req/tt.pdf');*/
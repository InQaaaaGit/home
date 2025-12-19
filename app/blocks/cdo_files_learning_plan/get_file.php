<?php

ini_set('max_execution_time', '300');

require_once __DIR__ . '/../../config.php';
global $CFG, $USER, $OUTPUT, $DB, $PAGE;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/classes/Api.php';
require_once __DIR__ . '/classes/Helpers.php';

require_login();
// Проверка прав доступа
require_capability('block/cdo_files_learning_plan:edit', context_system::instance());

function type_header($t)
{
    if ($t == 'jpg' or $t == 'jpeg') {
        header('Content-type: image/jpeg');
    } elseif ($t == 'png') {
        header('Content-type: image/png');
    } elseif ($t == 'gif') {
        header('Content-type: image/gif');
    } elseif ($t == 'pdf') {
        header('Content-type: application/pdf');
    } elseif ($t == 'zip') {
        header('Content-type: application/zip');
    } else {
        die(4);
    }
}

function clearStringTags($str)
{
    $str = strip_tags($str);
    $str = str_replace('\<([^>]+)>', '', $str);
    return $str;
}

$mode = optional_param("mode", "file", PARAM_TEXT);
$name = optional_param("name", "", PARAM_TEXT);

if($mode == 'file') {

    type_header("pdf");

    if(!empty($name))
        header("Content-Disposition:  inline; filename=\"$name\";");

    $urlParams = [
        "file_id" => optional_param("file_id", "", PARAM_TEXT),
    ];

    echo Api::getFile($urlParams);

} elseif ($mode == 'all_files'){

    type_header("zip");

    $urlParams = [
        "doc_number" => optional_param("doc_number", "", PARAM_TEXT),
    ];
    header("Content-Disposition:  attachment; filename=\"files_".$urlParams['doc_number'].".zip\";");

    Api::getAllFiles($urlParams);

}

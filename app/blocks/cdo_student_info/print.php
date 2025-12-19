<?php

use tool_cdo_config\di;

require_once __DIR__ . '/../../config.php';
require_login();

global $CFG;
require_once($CFG->dirroot . "/lib/filelib.php");

$file_id = required_param("file_id", PARAM_TEXT);
$extension = required_param("extension", PARAM_TEXT);
$filename = required_param("filename", PARAM_TEXT);
$fullfilename = $filename . '.' . $extension;
$render_widget = new \block_cdo_student_info\output\health\renderable();
$body = $render_widget->get_file_to_print($file_id);
$mime = mime_content_type($fullfilename);
ob_clean();
header("Content-Type: $mime");
/*header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');*/
header('Content-Disposition: attachment; filename="' . $fullfilename . '"');

echo($body);


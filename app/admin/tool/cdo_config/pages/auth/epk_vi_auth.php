<?php

require(__DIR__ . '/../../../../../config.php');

function decrypt_internal($str)
{
    $key = '3333692f593a3c775e694775';
    $str = base64_decode($str);
    $iv = "1234567812345678";
    $res = rtrim(openssl_decrypt($str, 'aes128', $key, 0, $iv), "\0");
    return trim($res);
}

global $DB, $CFG;

//$login = required_param("login", PARAM_RAW);
$user_id = required_param("user_id", PARAM_RAW);
$user_id = decrypt_internal($user_id);
$course_id = required_param("course", PARAM_RAW);
$course_id = decrypt_internal($course_id);
$token = optional_param("token", "", PARAM_RAW);
if (empty($user_id) or empty($course_id)) {
    redirect('/login/index.php', 'error', 0, \core\output\notification::NOTIFY_ERROR);
}
$USER = get_complete_user_data('id', $user_id);

$url = new moodle_url('/course/view.php', ['id' => $course_id]);
try {
    redirect($url);
} catch (moodle_exception $e) {
    redirect('/login/index.php', $e->getMessage());
}


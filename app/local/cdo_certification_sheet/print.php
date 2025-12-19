<?php

require_once __DIR__ . '/../../config.php';

$GUID = required_param("guid", PARAM_TEXT);
$return_url = optional_param('return_url', '', PARAM_URL);

$auth = base64_encode("hs_service_cdo:GY9xolug");
$url = 'http://10.128.240.232/university_volgmu/ru/hs/cdo_eois_Campus/getprintablestatementform?guid_statement=' . urlencode($GUID);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic {$auth}"
]);

$body = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($body === false) {
    $error_message = "Ошибка подключения к серверу: {$error}";
    if ($return_url) {
        redirect($return_url, $error_message, null, \core\output\notification::NOTIFY_ERROR);
    } else {
        echo $error_message;
    }
    exit;
}

if ($http_code !== 200) {
    $error_message = "Ошибка сервера: HTTP код {$http_code}";
    if ($return_url) {
        redirect($return_url, $error_message, null, \core\output\notification::NOTIFY_ERROR);
    } else {
        echo $error_message;
    }
    exit;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="Ведомость.docx"');
echo($body);

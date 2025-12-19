<?php
require_once __DIR__ . '/../../config.php';
require_login();
global $CFG;
require_once($CFG->dirroot . "/lib/filelib.php");

$curl = new curl();

// Получаем настройки блока
$url      = get_config('cdo_block_files_learning_plan', 'url');
$scenario = get_config('cdo_block_files_learning_plan', 'scenario');
$login    = get_config('cdo_block_files_learning_plan', 'hslogin');
$pass     = get_config('cdo_block_files_learning_plan', 'hspass');

// Устанавливаем авторизацию Basic через CURLOPT_USERPWD
$curl->setopt([
  'CURLOPT_USERPWD' => "$login:$pass",
  'CURLOPT_RETURNTRANSFER' => true, // для возврата ответа как строки
]);

// Получаем входные данные и отправляем запрос
$data = file_get_contents('php://input');
$response = $curl->post($url . $scenario, $data);

// Проверяем статус ответа
$http_code = $curl->get_info('http_code');
if ($http_code !== 200) {
  throw new coding_exception("Ошибка: $http_code. Ответ сервера: $response");
}

// Отправляем JSON-ответ
header('Content-type: application/json');
echo $response;

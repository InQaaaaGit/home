<?php

define('NO_DEBUG_DISPLAY', true);
define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../config.php');

use local_cdo_education_scoring\service\report_export_service;

// Увеличиваем лимиты для экспорта
@set_time_limit(0);
@ini_set('memory_limit', '512M');

$surveyid = required_param('surveyid', PARAM_INT);
$teacher_id = required_param('teacher_id', PARAM_INT);
$discipline_id = optional_param('discipline_id', null, PARAM_TEXT);

// Проверка авторизации
require_login();
$context = context_system::instance();
require_capability('local/cdo_education_scoring:manage', $context);

// Закрываем сессию перед длительной операцией
\core\session\manager::write_close();

// Очищаем буфер вывода
while (ob_get_level()) {
    ob_end_clean();
}

try {
    // Инициализация сервиса экспорта
    $exportService = new report_export_service();
    $exportService->export_survey_report($surveyid, $teacher_id, $discipline_id);
} catch (\Exception $e) {
    // Логируем ошибку
    error_log('Ошибка при экспорте отчета: ' . $e->getMessage());
    error_log('Трассировка: ' . $e->getTraceAsString());
    
    // Выводим сообщение об ошибке
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Ошибка при генерации отчета: ' . $e->getMessage();
    exit;
}

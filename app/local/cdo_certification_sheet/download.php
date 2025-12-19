<?php
require(__DIR__.'/../../config.php');

require_login();
global $CFG, $USER, $PAGE;
require_once($CFG->dirroot . "/lib/filelib.php");

use core\notification;
use tool_cdo_config\di;

try {
    // Проверяем наличие обязательного параметра

    $GUID = required_param("guid", PARAM_TEXT);
    $return_url = optional_param('return_url', '', PARAM_URL);
    if (empty($GUID)) {
        throw new Exception('Отсутствует обязательный параметр: $GUID');
    }

    global $CFG_CDO;

    $options = di::get_instance()->get_request_options();
    $options->set_properties([
        'guid_statement' => $GUID
    ]);

    $result = di::get_instance()
        ->get_request('download_sheet')
        ->request($options)
        ->get_request_result($print_body = true);

    $body = json_decode($result, true);
    if (!json_last_error()) {
        throw new Exception($body['message']);
    }
   /* if (empty($body)) {
        throw new Exception("Пустое тело");
    }*/
    // Успешное выполнение - отдаем файл
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="Ведомость.docx"');
    ob_clean();
    echo $result;
    exit;

} catch (Exception $e) {
    // Обработка общих исключений
    $PAGE->set_context(context_system::instance());
    $PAGE->set_url('/local/cdo_certification_sheet/download.php');

    // Логируем ошибку
    error_log("Download error: " . $e->getMessage() . " - User: " . $USER->id);

    // Определяем код ошибки на основе сообщения
    $errorcode = 'download_failed';
    $error_message = $e->getMessage();

    if (strpos($error_message, get_string('curlerror', 'local_cdo_order_documents')) !== false) {
        $errorcode = 'connection_error';
    } elseif (strpos($error_message, get_string('httperror', 'local_cdo_order_documents')) !== false) {
        $errorcode = 'connection_error';
    } elseif (strpos($error_message, get_string('emptyresponse', 'local_cdo_order_documents')) !== false) {
        $errorcode = 'invalid_certificate';
    } elseif (strpos($error_message, get_string('invalidformat', 'local_cdo_order_documents')) !== false) {
        $errorcode = 'invalid_certificate';
    } elseif (strpos($error_message, get_string('apierror', 'local_cdo_order_documents')) !== false) {
        $errorcode = 'invalid_certificate';
    }

    // Перенаправляем на страницу ошибок
    $errorurl = new moodle_url('/local/cdo_certification_sheet/error.php', [
        'code' => $errorcode,
        'message' => json_encode($error_message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'return' => 'index'
    ]);
    redirect($errorurl);
}

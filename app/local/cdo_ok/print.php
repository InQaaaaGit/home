<?php

use local_cdo_ok\controllers\answers_controller;
use local_cdo_ok\controllers\questions_controller;
use local_cdo_ok\reports\printer;
use local_cdo_ok\reports\variants\report1;
use local_cdo_ok\reports\variants\report2;
use local_cdo_ok\reports\variants\report3;
use local_cdo_ok\reports\variants\report4;
use local_cdo_ok\reports\variants\report5;
use local_cdo_ok\reports\variants\report6;


require_once(__DIR__ . "/../../config.php");

require_login();

try {
    $report = required_param("report", PARAM_TEXT);
    
    // Валидация класса отчета
    $allowed_reports = [
        'local_cdo_ok\\reports\\variants\\report1',
        'local_cdo_ok\\reports\\variants\\report2',
        'local_cdo_ok\\reports\\variants\\report3',
        'local_cdo_ok\\reports\\variants\\report4',
        'local_cdo_ok\\reports\\variants\\report5',
        'local_cdo_ok\\reports\\variants\\report6',
    ];
    
    if (!in_array($report, $allowed_reports)) {
        throw new moodle_exception('invalidreport', 'local_cdo_ok');
    }
    
    // Проверка что класс существует
    if (!class_exists($report)) {
        throw new moodle_exception('reportclassnotfound', 'local_cdo_ok', '', $report);
    }
    
    $start_time = microtime(true);
    
    $printer = new printer(
        new $report(new questions_controller(), new answers_controller())
    );
    
    $printer->print();
    
    $execution_time = microtime(true) - $start_time;
    debugging(
        sprintf('Report generated in %.2f seconds', $execution_time),
        DEBUG_DEVELOPER
    );
    
} catch (Throwable $e) {
    // Логирование ошибки
    debugging(
        sprintf(
            'Error generating report: %s in %s:%d',
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ),
        DEBUG_DEVELOPER
    );
    
    // Очищаем буферы вывода
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Отправляем понятное сообщение пользователю
    if (debugging('', DEBUG_DEVELOPER)) {
        print_error(
            'errorgeneratingreport',
            'local_cdo_ok',
            '',
            $e->getMessage()
        );
    } else {
        print_error('errorgeneratingreport', 'local_cdo_ok');
    }
}


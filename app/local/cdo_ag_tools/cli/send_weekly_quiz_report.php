<?php
/**
 * CLI скрипт для отправки еженедельного отчета о пройденных тестах конкретному пользователю
 *
 * Usage:
 *   php send_weekly_quiz_report.php --userid=123
 *   php send_weekly_quiz_report.php --userid=123 --datefrom=2025-10-20 --dateto=2025-10-26
 *
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

use local_cdo_ag_tools\services\work_notification_service;

// Определяем параметры командной строки
$usage = "Отправка еженедельного отчета о пройденных тестах пользователю

Usage:
    php send_weekly_quiz_report.php --userid=<user_id> [--datefrom=<date>] [--dateto=<date>]

Options:
    --userid=<user_id>      ID пользователя (обязательный параметр)
    --datefrom=<date>       Дата начала периода в формате Y-m-d (по умолчанию: начало текущей недели)
    --dateto=<date>         Дата окончания периода в формате Y-m-d (по умолчанию: конец текущей недели)
    -h, --help              Показать это сообщение

Examples:
    # Отправить отчет за текущую неделю
    php send_weekly_quiz_report.php --userid=123

    # Отправить отчет за указанный период
    php send_weekly_quiz_report.php --userid=123 --datefrom=2025-10-20 --dateto=2025-10-26

    # Отправить отчет за прошлую неделю
    php send_weekly_quiz_report.php --userid=123 --datefrom=2025-10-13 --dateto=2025-10-19
";

list($options, $unrecognized) = cli_get_params(
    [
        'userid' => null,
        'datefrom' => null,
        'dateto' => null,
        'help' => false,
    ],
    [
        'h' => 'help',
    ]
);

// Показываем help если запрошен
if ($options['help']) {
    echo $usage;
    exit(0);
}

// Проверяем обязательный параметр userid
if (empty($options['userid'])) {
    cli_error("Ошибка: параметр --userid обязателен\n\n" . $usage);
}

$userId = (int)$options['userid'];

// Проверяем существование пользователя
$user = $DB->get_record('user', ['id' => $userId], '*', IGNORE_MISSING);
if (!$user) {
    cli_error("Ошибка: пользователь с ID {$userId} не найден");
}

if ($user->deleted) {
    cli_error("Ошибка: пользователь с ID {$userId} удален");
}

// Определяем период
if ($options['datefrom'] && $options['dateto']) {
    // Используем указанные даты
    $dateFrom = $options['datefrom'];
    $dateTo = $options['dateto'];
    
    // Валидация формата дат
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
        cli_error("Ошибка: неверный формат даты --datefrom. Используйте формат Y-m-d (например, 2025-10-20)");
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
        cli_error("Ошибка: неверный формат даты --dateto. Используйте формат Y-m-d (например, 2025-10-26)");
    }
    
    // Проверяем корректность дат
    if (strtotime($dateFrom) === false) {
        cli_error("Ошибка: некорректная дата --datefrom: {$dateFrom}");
    }
    if (strtotime($dateTo) === false) {
        cli_error("Ошибка: некорректная дата --dateto: {$dateTo}");
    }
    if (strtotime($dateFrom) > strtotime($dateTo)) {
        cli_error("Ошибка: дата начала периода должна быть раньше даты окончания");
    }
} else {
    // Определяем текущую неделю (понедельник - воскресенье)
    $currentTimestamp = time();
    $dayOfWeek = date('N', $currentTimestamp); // 1 (понедельник) - 7 (воскресенье)
    
    // Начало недели (понедельник)
    $startOfWeek = strtotime('-' . ($dayOfWeek - 1) . ' days', $currentTimestamp);
    $dateFrom = date('Y-m-d', $startOfWeek);
    
    // Конец недели (воскресенье)
    $endOfWeek = strtotime('+' . (7 - $dayOfWeek) . ' days', $currentTimestamp);
    $dateTo = date('Y-m-d', $endOfWeek);
}

// Выводим информацию о запуске
cli_heading("Отправка еженедельного отчета о пройденных тестах");
echo "Пользователь: {$user->firstname} {$user->lastname} (ID: {$userId}, Email: {$user->email})\n";
echo "Период: с {$dateFrom} по {$dateTo}\n";
echo str_repeat('-', 80) . "\n\n";

// Проверяем наличие оценок за период
$timeFrom = strtotime($dateFrom);
$timeTo = strtotime($dateTo . ' 23:59:59');

$sql = "SELECT COUNT(DISTINCT gg.id)
        FROM {grade_grades} gg
        JOIN {grade_items} gi ON gi.id = gg.itemid
        WHERE gg.userid = :userid
          AND gi.itemtype = 'mod'
          AND gi.itemmodule = 'quiz'
          AND gg.finalgrade IS NOT NULL
          AND gg.timemodified >= :timefrom
          AND gg.timemodified <= :timeto";

$gradesCount = $DB->count_records_sql($sql, [
    'userid' => $userId,
    'timefrom' => $timeFrom,
    'timeto' => $timeTo,
]);

if ($gradesCount == 0) {
    echo "⚠️  У пользователя нет оценок за тесты в указанном периоде.\n";
    echo "Письмо не будет отправлено.\n";
    exit(0);
}

echo "✓ Найдено оценок за тесты: {$gradesCount}\n\n";

// Отправляем отчет
try {
    echo "Отправка отчета...\n";
    
    $result = work_notification_service::send_weekly_quiz_report(
        $userId,
        $dateFrom,
        $dateTo
    );
    
    if ($result) {
        echo "\n";
        cli_heading("✅ Успешно!", null, 'green');
        echo "Отчет успешно отправлен на email: {$user->email}\n";
        exit(0);
    } else {
        echo "\n";
        cli_heading("❌ Ошибка", null, 'red');
        echo "Не удалось отправить отчет. Возможные причины:\n";
        echo "  - У пользователя не указан email\n";
        echo "  - Нет оценок за указанный период\n";
        echo "  - Ошибка при отправке сообщения\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "\n";
    cli_heading("❌ Исключение", null, 'red');
    echo "Ошибка при отправке отчета: " . $e->getMessage() . "\n";
    echo "Трассировка:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}


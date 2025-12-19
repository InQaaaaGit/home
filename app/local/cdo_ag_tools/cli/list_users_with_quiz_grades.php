<?php
/**
 * CLI скрипт для получения списка пользователей с оценками за тесты за указанный период
 *
 * Usage:
 *   php list_users_with_quiz_grades.php
 *   php list_users_with_quiz_grades.php --datefrom=2025-10-20 --dateto=2025-10-26
 *   php list_users_with_quiz_grades.php --detailed
 *
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// Определяем параметры командной строки
$usage = "Получение списка пользователей с оценками за тесты за указанный период

Usage:
    php list_users_with_quiz_grades.php [--datefrom=<date>] [--dateto=<date>] [--detailed]

Options:
    --datefrom=<date>       Дата начала периода в формате Y-m-d (по умолчанию: начало текущей недели)
    --dateto=<date>         Дата окончания периода в формате Y-m-d (по умолчанию: конец текущей недели)
    --detailed              Показать детальную информацию о каждом пользователе
    -h, --help              Показать это сообщение

Examples:
    # Список пользователей за текущую неделю
    php list_users_with_quiz_grades.php

    # Список пользователей за указанный период
    php list_users_with_quiz_grades.php --datefrom=2025-10-20 --dateto=2025-10-26

    # Детальная информация о пользователях
    php list_users_with_quiz_grades.php --detailed
";

list($options, $unrecognized) = cli_get_params(
    [
        'datefrom' => null,
        'dateto' => null,
        'detailed' => false,
        'help' => false,
    ],
    [
        'h' => 'help',
        'd' => 'detailed',
    ]
);

// Показываем help если запрошен
if ($options['help']) {
    echo $usage;
    exit(0);
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

// Выводим заголовок
cli_heading("Список пользователей с оценками за тесты");
echo "Период: с {$dateFrom} по {$dateTo}\n";
echo str_repeat('-', 80) . "\n\n";

// Получаем данные
$timeFrom = strtotime($dateFrom);
$timeTo = strtotime($dateTo . ' 23:59:59');

if ($options['detailed']) {
    // Детальная информация: пользователь + количество оценок + список курсов
    $sql = "SELECT u.id, 
                   u.username, 
                   u.firstname, 
                   u.lastname, 
                   u.email,
                   u.idnumber,
                   COUNT(DISTINCT gg.id) as grades_count,
                   COUNT(DISTINCT c.id) as courses_count,
                   GROUP_CONCAT(DISTINCT c.fullname SEPARATOR '; ') as course_names
            FROM {user} u
            JOIN {grade_grades} gg ON gg.userid = u.id
            JOIN {grade_items} gi ON gi.id = gg.itemid
            JOIN {course} c ON c.id = gi.courseid
            WHERE gi.itemtype = 'mod'
              AND gi.itemmodule = 'quiz'
              AND gg.finalgrade IS NOT NULL
              AND gg.timemodified >= :timefrom
              AND gg.timemodified <= :timeto
              AND u.deleted = 0
            GROUP BY u.id, u.username, u.firstname, u.lastname, u.email, u.idnumber
            ORDER BY u.lastname, u.firstname";
    
    $users = $DB->get_records_sql($sql, [
        'timefrom' => $timeFrom,
        'timeto' => $timeTo,
    ]);
    
    if (empty($users)) {
        echo "Пользователей с оценками за тесты в указанном периоде не найдено.\n";
        exit(0);
    }
    
    echo "Найдено пользователей: " . count($users) . "\n\n";
    echo str_repeat('=', 80) . "\n\n";
    
    foreach ($users as $user) {
        echo "ID: {$user->id}\n";
        echo "Пользователь: {$user->firstname} {$user->lastname} ({$user->username})\n";
        echo "Email: {$user->email}\n";
        
        if (!empty($user->idnumber)) {
            echo "ID Number: {$user->idnumber}\n";
        }
        
        echo "Оценок за тесты: {$user->grades_count}\n";
        echo "Курсов: {$user->courses_count}\n";
        
        // Ограничиваем длину списка курсов для читаемости
        $courseNames = $user->course_names;
        if (strlen($courseNames) > 100) {
            $courseNames = substr($courseNames, 0, 97) . '...';
        }
        echo "Курсы: {$courseNames}\n";
        
        echo "\n" . str_repeat('-', 80) . "\n\n";
    }
    
    // Итоговая статистика
    $totalGrades = array_sum(array_column($users, 'grades_count'));
    $totalCourses = array_sum(array_column($users, 'courses_count'));
    
    echo "\n";
    cli_heading("Статистика");
    echo "Всего пользователей: " . count($users) . "\n";
    echo "Всего оценок: {$totalGrades}\n";
    echo "Всего связей с курсами: {$totalCourses}\n";
    echo "Средняя оценок на пользователя: " . round($totalGrades / count($users), 1) . "\n";
    
} else {
    // Простой список: только ID пользователей
    $sql = "SELECT DISTINCT u.id, u.username, u.firstname, u.lastname, u.email
            FROM {user} u
            JOIN {grade_grades} gg ON gg.userid = u.id
            JOIN {grade_items} gi ON gi.id = gg.itemid
            WHERE gi.itemtype = 'mod'
              AND gi.itemmodule = 'quiz'
              AND gg.finalgrade IS NOT NULL
              AND gg.timemodified >= :timefrom
              AND gg.timemodified <= :timeto
              AND u.deleted = 0
            ORDER BY u.id";
    
    $users = $DB->get_records_sql($sql, [
        'timefrom' => $timeFrom,
        'timeto' => $timeTo,
    ]);
    
    if (empty($users)) {
        echo "Пользователей с оценками за тесты в указанном периоде не найдено.\n";
        exit(0);
    }
    
    echo "Найдено пользователей: " . count($users) . "\n\n";
    
    // Выводим таблицу
    $table = new cli_table();
    $table->set_attribute('class', 'generaltable');
    
    $headers = ['ID', 'Username', 'ФИО', 'Email'];
    $table->set_attribute('border', 1);
    
    echo sprintf("%-8s %-20s %-30s %-30s\n", 'ID', 'Username', 'ФИО', 'Email');
    echo str_repeat('-', 90) . "\n";
    
    foreach ($users as $user) {
        $fullName = trim($user->firstname . ' ' . $user->lastname);
        
        // Ограничиваем длину для читаемости
        $username = strlen($user->username) > 18 ? substr($user->username, 0, 15) . '...' : $user->username;
        $fullName = strlen($fullName) > 28 ? substr($fullName, 0, 25) . '...' : $fullName;
        $email = strlen($user->email) > 28 ? substr($user->email, 0, 25) . '...' : $user->email;
        
        echo sprintf("%-8s %-20s %-30s %-30s\n", $user->id, $username, $fullName, $email);
    }
    
    echo "\n";
    echo "Всего: " . count($users) . " пользователей\n\n";
    
    // Подсказка
    echo "💡 Подсказка: используйте --detailed для получения детальной информации\n";
    echo "💡 Для отправки отчета конкретному пользователю используйте:\n";
    echo "   php send_weekly_quiz_report.php --userid=<ID>\n";
}


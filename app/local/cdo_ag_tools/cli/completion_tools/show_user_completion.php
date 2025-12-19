<?php
/**
 * CLI скрипт для просмотра информации о completion конкретного пользователя
 *
 * Быстрый способ посмотреть все completion статусы пользователя в курсе
 *
 * Использование:
 *   php show_user_completion.php --courseid=123 --userid=45
 *
 * @package    local_cdo_ag_tools
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/completionlib.php');

// Получаем параметры командной строки
list($options, $unrecognized) = cli_get_params(
    [
        'help' => false,
        'courseid' => null,
        'userid' => null,
        'username' => null,
    ],
    [
        'h' => 'help',
        'c' => 'courseid',
        'u' => 'userid',
        'n' => 'username',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
Скрипт для просмотра информации о completion конкретного пользователя.

Использование:
    php show_user_completion.php [параметры]

Параметры:
    -h, --help              Показать эту справку
    -c, --courseid=ID       ID курса (обязательный)
    -u, --userid=ID         ID пользователя
    -n, --username=NAME     Username пользователя (альтернатива userid)

Примеры:
    # Просмотр completion по ID пользователя
    php show_user_completion.php --courseid=123 --userid=45

    # Просмотр completion по username
    php show_user_completion.php --courseid=123 --username=student1

EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

// Валидация параметров
if (empty($options['courseid'])) {
    cli_error('Необходимо указать --courseid');
}

if (empty($options['userid']) && empty($options['username'])) {
    cli_error('Необходимо указать --userid или --username');
}

$courseid = intval($options['courseid']);

// Получаем пользователя
if (!empty($options['userid'])) {
    $userid = intval($options['userid']);
    $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
} else {
    $username = $options['username'];
    $user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);
    $userid = $user->id;
}

// Получаем курс
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$completion_info = new completion_info($course);

// Заголовок
cli_heading("Информация о completion пользователя");
cli_writeln("═══════════════════════════════════════════════════════════════");
cli_writeln("Пользователь: {$user->firstname} {$user->lastname}");
cli_writeln("Username:     {$user->username}");
cli_writeln("User ID:      {$user->id}");
cli_writeln("Email:        {$user->email}");
cli_writeln("");
cli_writeln("Курс:         {$course->fullname}");
cli_writeln("Course ID:    {$course->id}");
cli_writeln("═══════════════════════════════════════════════════════════════");

if (!$completion_info->is_enabled()) {
    cli_error('Completion отключен для данного курса');
}

// Проверяем, записан ли пользователь на курс
$context = context_course::instance($course->id);
if (!is_enrolled($context, $user)) {
    cli_problem('ВНИМАНИЕ: Пользователь не записан на данный курс!');
}

// Получаем информацию о completion курса
$course_completion = $DB->get_record('course_completions', [
    'course' => $courseid,
    'userid' => $userid,
]);

cli_writeln("\n" . str_repeat("─", 65));
cli_writeln("COMPLETION КУРСА");
cli_writeln(str_repeat("─", 65));

if ($course_completion) {
    $status = 'Не завершен';
    if ($course_completion->timecompleted) {
        $status = 'Завершен: ' . userdate($course_completion->timecompleted);
    }
    cli_writeln("Статус: {$status}");
    cli_writeln("Начат:  " . ($course_completion->timestarted ? userdate($course_completion->timestarted) : 'Нет данных'));
} else {
    cli_writeln("Статус: Нет данных о completion курса");
}

// Получаем информацию о модулях
$modinfo = get_fast_modinfo($course);
$cms = $modinfo->get_cms();

// Собираем статистику
$stats = [
    'total' => 0,
    'with_tracking' => 0,
    'complete' => 0,
    'complete_pass' => 0,
    'complete_fail' => 0,
    'incomplete' => 0,
];

$completion_details = [];

foreach ($cms as $cm) {
    if ($cm->completion == COMPLETION_TRACKING_NONE) {
        continue;
    }
    
    $stats['with_tracking']++;
    
    $completion_data = $completion_info->get_data($cm, false, $userid);
    
    $detail = [
        'cm' => $cm,
        'completion_data' => $completion_data,
    ];
    
    switch ($completion_data->completionstate) {
        case COMPLETION_COMPLETE:
            $stats['complete']++;
            break;
        case COMPLETION_COMPLETE_PASS:
            $stats['complete_pass']++;
            break;
        case COMPLETION_COMPLETE_FAIL:
            $stats['complete_fail']++;
            break;
        case COMPLETION_INCOMPLETE:
            $stats['incomplete']++;
            break;
    }
    
    $completion_details[] = $detail;
}

// Выводим статистику
cli_writeln("\n" . str_repeat("─", 65));
cli_writeln("СТАТИСТИКА COMPLETION ЭЛЕМЕНТОВ");
cli_writeln(str_repeat("─", 65));
cli_writeln("Всего элементов с отслеживанием: {$stats['with_tracking']}");
cli_writeln("  ✓ Завершено:                   {$stats['complete']}");
cli_writeln("  ✓ Завершено (сдано):           {$stats['complete_pass']}");
cli_writeln("  ✗ Завершено (не сдано):        {$stats['complete_fail']}");
cli_writeln("  ○ Не завершено:                {$stats['incomplete']}");

// Выводим детальную информацию о каждом элементе
cli_writeln("\n" . str_repeat("─", 65));
cli_writeln("ДЕТАЛИ ПО ЭЛЕМЕНТАМ КУРСА");
cli_writeln(str_repeat("─", 65));

if (empty($completion_details)) {
    cli_writeln("Нет элементов с отслеживанием completion");
} else {
    foreach ($completion_details as $detail) {
        $cm = $detail['cm'];
        $completion_data = $detail['completion_data'];
        
        // Символ статуса
        $status_symbol = '○';
        switch ($completion_data->completionstate) {
            case COMPLETION_COMPLETE:
                $status_symbol = '✓';
                break;
            case COMPLETION_COMPLETE_PASS:
                $status_symbol = '✓';
                break;
            case COMPLETION_COMPLETE_FAIL:
                $status_symbol = '✗';
                break;
        }
        
        cli_writeln("");
        cli_writeln("{$status_symbol} [{$cm->modname}] {$cm->name}");
        cli_writeln("  CM ID:       {$cm->id}");
        cli_writeln("  Статус:      " . get_completion_state_name($completion_data->completionstate));
        cli_writeln("  Просмотрено: " . ($completion_data->viewed ? 'Да' : 'Нет'));
        
        if ($completion_data->timemodified) {
            cli_writeln("  Изменено:    " . userdate($completion_data->timemodified));
        }
        
        // Дополнительная информация о требованиях
        $requirements = [];
        if ($cm->completionview == COMPLETION_VIEW_REQUIRED) {
            $requirements[] = "Требуется просмотр";
        }
        if ($cm->completiongradeitemnumber !== null) {
            $requirements[] = "Требуется оценка";
            
            if ($cm->completionpassgrade) {
                $requirements[] = "Требуется проходной балл";
            }
        }
        if ($cm->completionexpected) {
            $requirements[] = "Ожидаемое завершение: " . userdate($cm->completionexpected);
        }
        
        if (!empty($requirements)) {
            cli_writeln("  Требования:  " . implode(", ", $requirements));
        }
        
        // Информация об оценке, если есть
        if ($cm->completiongradeitemnumber !== null) {
            $grade = grade_get_grades($cm->course, 'mod', $cm->modname, $cm->instance, $userid);
            if (!empty($grade->items[0]->grades[$userid]->grade)) {
                $user_grade = $grade->items[0]->grades[$userid];
                $grade_item = $grade->items[0];
                
                $grade_str = sprintf("%.2f", $user_grade->grade);
                if ($grade_item->grademax > 0) {
                    $grade_str .= " / " . sprintf("%.2f", $grade_item->grademax);
                }
                if ($grade_item->gradepass > 0) {
                    $grade_str .= " (проходной: " . sprintf("%.2f", $grade_item->gradepass) . ")";
                }
                
                cli_writeln("  Оценка:      {$grade_str}");
            }
        }
    }
}

cli_writeln("\n" . str_repeat("═", 65));
cli_writeln("Готово!");

exit(0);

/**
 * Получает название статуса completion
 *
 * @param int $state Код статуса
 * @return string Название статуса
 */
function get_completion_state_name($state) {
    switch ($state) {
        case COMPLETION_INCOMPLETE:
            return 'Не завершено';
        case COMPLETION_COMPLETE:
            return 'Завершено';
        case COMPLETION_COMPLETE_PASS:
            return 'Завершено (сдано)';
        case COMPLETION_COMPLETE_FAIL:
            return 'Завершено (не сдано)';
        default:
            return 'Неизвестно';
    }
}


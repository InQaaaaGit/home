<?php
/**
 * CLI скрипт для анализа статусов completion и выявления несоответствий
 *
 * Этот скрипт помогает найти пользователей, у которых статус completion
 * выставлен некорректно (не соответствует критериям завершения)
 *
 * Использование:
 * - Анализ completion в конкретном курсе:
 *   php analyze_completion.php --courseid=123
 *
 * - Анализ completion для конкретных пользователей:
 *   php analyze_completion.php --courseid=123 --userids=1,2,3
 *
 * - Анализ с подробным выводом:
 *   php analyze_completion.php --courseid=123 --verbose
 *
 * - Экспорт результатов в CSV:
 *   php analyze_completion.php --courseid=123 --export=report.csv
 *
 * @package    local_cdo_ag_tools
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/gradelib.php');

// Получаем параметры командной строки
list($options, $unrecognized) = cli_get_params(
    [
        'help' => false,
        'courseid' => null,
        'userids' => null,
        'verbose' => false,
        'export' => null,
        'show-valid' => false,
    ],
    [
        'h' => 'help',
        'c' => 'courseid',
        'u' => 'userids',
        'v' => 'verbose',
        'e' => 'export',
        's' => 'show-valid',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
Скрипт для анализа статусов completion и выявления несоответствий.

Использование:
    php analyze_completion.php [параметры]

Параметры:
    -h, --help              Показать эту справку
    -c, --courseid=ID       ID курса для анализа (обязательный)
    -u, --userids=IDS       Список ID пользователей через запятую (если не указано - все)
    -v, --verbose           Подробный вывод
    -e, --export=FILE       Экспортировать результаты в CSV файл
    -s, --show-valid        Показывать также корректные completion (по умолчанию только проблемные)

Примеры:
    # Анализ completion в курсе 123
    php analyze_completion.php --courseid=123

    # Анализ конкретных пользователей с подробным выводом
    php analyze_completion.php --courseid=123 --userids=1,2,3 --verbose

    # Экспорт результатов в CSV
    php analyze_completion.php --courseid=123 --export=completion_report.csv

EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

// Валидация параметров
if (empty($options['courseid'])) {
    cli_error('Необходимо указать --courseid');
}

$courseid = intval($options['courseid']);
$userids = [];
$isVerbose = $options['verbose'];
$exportFile = $options['export'];
$showValid = $options['show-valid'];

// Получаем курс
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$completion_info = new completion_info($course);

cli_heading("Анализ completion в курсе: {$course->fullname} (ID: {$course->id})");

if (!$completion_info->is_enabled()) {
    cli_error('Completion отключен для данного курса');
}

// Получаем список пользователей
if (!empty($options['userids'])) {
    $userids = array_map('intval', explode(',', $options['userids']));
    cli_writeln("Анализ пользователей: " . implode(', ', $userids));
} else {
    $context = context_course::instance($course->id);
    $enrolled = get_enrolled_users($context, '', 0, 'u.id');
    $userids = array_keys($enrolled);
    cli_writeln("Найдено записанных пользователей: " . count($userids));
}

if (empty($userids)) {
    cli_error('Не найдено пользователей для анализа');
}

// Статистика
$stats = [
    'total_users' => 0,
    'total_completions' => 0,
    'invalid_completions' => 0,
    'valid_completions' => 0,
    'issues_by_type' => [],
];

// Массив для экспорта
$export_data = [];
$export_data[] = ['User ID', 'Username', 'CM ID', 'Module Name', 'Completion State', 'Issue Type', 'Details'];

// Получаем информацию о модулях курса
$modinfo = get_fast_modinfo($course);
$cms = $modinfo->get_cms();

cli_writeln("\nАнализ completion записей...\n");

// Анализируем каждого пользователя
foreach ($userids as $userid) {
    $user = $DB->get_record('user', ['id' => $userid]);
    
    if (!$user) {
        cli_problem("Пользователь ID {$userid} не найден");
        continue;
    }
    
    $stats['total_users']++;
    
    if ($isVerbose) {
        cli_writeln("Пользователь: {$user->username} ({$user->firstname} {$user->lastname}, ID: {$user->id})");
    }
    
    $user_issues = [];
    
    // Проверяем каждый модуль курса
    foreach ($cms as $cm) {
        if ($cm->completion == COMPLETION_TRACKING_NONE) {
            continue; // Пропускаем модули без отслеживания completion
        }
        
        // Получаем текущий статус completion
        $completion_data = $completion_info->get_data($cm, false, $userid);
        
        if ($completion_data->completionstate == COMPLETION_INCOMPLETE) {
            continue; // Пропускаем незавершенные (это норма)
        }
        
        $stats['total_completions']++;
        
        // Проверяем соответствие критериям
        $issue = check_completion_validity($cm, $completion_data, $user, $completion_info);
        
        if ($issue) {
            $stats['invalid_completions']++;
            $user_issues[] = $issue;
            
            // Подсчет типов проблем
            if (!isset($stats['issues_by_type'][$issue['type']])) {
                $stats['issues_by_type'][$issue['type']] = 0;
            }
            $stats['issues_by_type'][$issue['type']]++;
            
            // Вывод информации о проблеме
            if (!$isVerbose) {
                cli_writeln("❌ Пользователь ID {$userid} ({$user->username}): " .
                           "CM ID {$cm->id} ({$cm->name}) - {$issue['description']}");
            } else {
                cli_writeln("  ❌ CM ID {$cm->id}: {$cm->name}");
                cli_writeln("     Статус: " . get_completion_state_name($completion_data->completionstate));
                cli_writeln("     Проблема: {$issue['description']}");
                cli_writeln("     Детали: {$issue['details']}");
            }
            
            // Добавляем в экспорт
            $export_data[] = [
                $user->id,
                $user->username,
                $cm->id,
                $cm->name,
                get_completion_state_name($completion_data->completionstate),
                $issue['type'],
                $issue['details'],
            ];
        } else {
            $stats['valid_completions']++;
            
            if ($showValid && $isVerbose) {
                cli_writeln("  ✓ CM ID {$cm->id}: {$cm->name} - корректно");
            }
        }
    }
    
    if ($isVerbose && empty($user_issues)) {
        cli_writeln("  ✓ Все completion корректны");
    }
    
    if ($isVerbose) {
        cli_writeln("");
    }
}

// Выводим статистику
cli_heading('Статистика анализа:', 2);
cli_writeln("Всего пользователей: {$stats['total_users']}");
cli_writeln("Всего completion записей: {$stats['total_completions']}");
cli_writeln("Корректных completion: {$stats['valid_completions']}");
cli_writeln("Некорректных completion: {$stats['invalid_completions']}");

if (!empty($stats['issues_by_type'])) {
    cli_writeln("\nПроблемы по типам:");
    foreach ($stats['issues_by_type'] as $type => $count) {
        cli_writeln("  - {$type}: {$count}");
    }
}

// Экспорт в CSV
if ($exportFile && $stats['invalid_completions'] > 0) {
    cli_writeln("\nЭкспорт результатов в {$exportFile}...");
    
    $fp = fopen($exportFile, 'w');
    if ($fp) {
        foreach ($export_data as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        cli_writeln("✓ Результаты экспортированы");
    } else {
        cli_problem("Не удалось создать файл {$exportFile}");
    }
}

// Рекомендации
if ($stats['invalid_completions'] > 0) {
    cli_heading('Рекомендации:', 2);
    cli_writeln("Найдено {$stats['invalid_completions']} некорректных completion записей.");
    cli_writeln("Для исправления используйте скрипт reset_completion.php");
    cli_writeln("\nПример:");
    
    // Собираем уникальные ID пользователей с проблемами
    $problematic_userids = [];
    foreach ($export_data as $row) {
        if ($row[0] !== 'User ID' && !in_array($row[0], $problematic_userids)) {
            $problematic_userids[] = $row[0];
        }
    }
    
    if (!empty($problematic_userids)) {
        $userids_str = implode(',', array_slice($problematic_userids, 0, 10));
        if (count($problematic_userids) > 10) {
            $userids_str .= ',...';
        }
        cli_writeln("php reset_completion.php --courseid={$courseid} --userids={$userids_str} --recalculate");
    }
}

cli_writeln("\nГотово!");
exit(0);

// ========== Вспомогательные функции ==========

/**
 * Проверяет соответствие completion критериям завершения
 *
 * @param cm_info $cm Информация о модуле курса
 * @param stdClass $completion_data Данные completion
 * @param stdClass $user Пользователь
 * @param completion_info $completion_info Объект completion
 * @return array|null Массив с описанием проблемы или null если все корректно
 */
function check_completion_validity($cm, $completion_data, $user, $completion_info) {
    global $DB;
    
    $issues = [];
    
    // 1. Проверка требования просмотра
    if ($cm->completionview == COMPLETION_VIEW_REQUIRED) {
        if (!$completion_data->viewed) {
            return [
                'type' => 'Not Viewed',
                'description' => 'Требуется просмотр, но не просмотрено',
                'details' => 'Модуль требует просмотра, но нет записи о просмотре пользователем',
            ];
        }
    }
    
    // 2. Проверка требования оценки
    if ($cm->completiongradeitemnumber !== null) {
        $grade = grade_get_grades($cm->course, 'mod', $cm->modname, $cm->instance, $user->id);
        
        if (empty($grade->items[0]->grades[$user->id]->grade)) {
            return [
                'type' => 'No Grade',
                'description' => 'Требуется оценка, но оценка отсутствует',
                'details' => 'Модуль требует оценки для завершения, но оценка не выставлена',
            ];
        }
        
        // Проверка условия сдачи (pass grade)
        if ($cm->completionpassgrade) {
            $user_grade = $grade->items[0]->grades[$user->id];
            $grade_item = $grade->items[0];
            
            if ($grade_item->gradepass > 0) {
                $user_grade_value = $user_grade->grade;
                
                if ($user_grade_value < $grade_item->gradepass) {
                    if ($completion_data->completionstate == COMPLETION_COMPLETE_PASS) {
                        return [
                            'type' => 'Grade Below Pass',
                            'description' => 'Оценка ниже проходного балла',
                            'details' => sprintf(
                                'Оценка %.2f меньше проходного балла %.2f, но статус "Завершено (сдано)"',
                                $user_grade_value,
                                $grade_item->gradepass
                            ),
                        ];
                    }
                }
            }
        }
    }
    
    // 3. Проверка кастомных критериев completion
    if ($cm->completionexpected) {
        // Проверяем дату ожидаемого завершения
        if ($completion_data->timemodified > $cm->completionexpected + 86400) {
            // Если completion выставлен более чем через день после ожидаемой даты
            $issues[] = 'Завершено позже ожидаемой даты';
        }
    }
    
    // 4. Проверка специфичных для модулей требований
    switch ($cm->modname) {
        case 'assign':
            // Проверка наличия работы
            $submission = $DB->get_record('assign_submission', [
                'assignment' => $cm->instance,
                'userid' => $user->id,
                'status' => 'submitted',
            ]);
            
            if (!$submission && $completion_data->completionstate != COMPLETION_INCOMPLETE) {
                return [
                    'type' => 'No Submission',
                    'description' => 'Нет отправленной работы',
                    'details' => 'Задание помечено завершенным, но работа не отправлена',
                ];
            }
            break;
            
        case 'quiz':
            // Проверка попыток прохождения теста
            $attempts = $DB->get_records('quiz_attempts', [
                'quiz' => $cm->instance,
                'userid' => $user->id,
                'state' => 'finished',
            ]);
            
            if (empty($attempts) && $completion_data->completionstate != COMPLETION_INCOMPLETE) {
                return [
                    'type' => 'No Quiz Attempts',
                    'description' => 'Нет завершенных попыток теста',
                    'details' => 'Тест помечен завершенным, но нет завершенных попыток',
                ];
            }
            break;
            
        case 'forum':
            // Проверка требований форума (например, количество сообщений)
            $cm_record = $DB->get_record('course_modules', ['id' => $cm->id]);
            if ($cm_record->completionposts) {
                $posts_count = $DB->count_records_sql(
                    "SELECT COUNT(*)
                     FROM {forum_posts} fp
                     JOIN {forum_discussions} fd ON fd.id = fp.discussion
                     WHERE fd.forum = :forumid AND fp.userid = :userid",
                    ['forumid' => $cm->instance, 'userid' => $user->id]
                );
                
                if ($posts_count < $cm_record->completionposts) {
                    return [
                        'type' => 'Insufficient Posts',
                        'description' => 'Недостаточно сообщений на форуме',
                        'details' => sprintf(
                            'Требуется %d сообщений, создано только %d',
                            $cm_record->completionposts,
                            $posts_count
                        ),
                    ];
                }
            }
            break;
    }
    
    // 5. Проверка логической целостности статусов
    if ($completion_data->completionstate == COMPLETION_COMPLETE_PASS ||
        $completion_data->completionstate == COMPLETION_COMPLETE_FAIL) {
        // Эти статусы должны быть только если есть требование оценки
        if ($cm->completiongradeitemnumber === null) {
            return [
                'type' => 'Invalid Pass/Fail State',
                'description' => 'Некорректный статус сдачи',
                'details' => 'Статус "сдано/не сдано" без требования оценки',
            ];
        }
    }
    
    return null; // Все проверки пройдены
}

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


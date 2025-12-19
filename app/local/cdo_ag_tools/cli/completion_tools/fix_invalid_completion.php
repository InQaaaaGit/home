<?php
/**
 * CLI скрипт для умного пересчета completion
 * 
 * Анализирует completion, находит только ОШИБОЧНЫЕ записи и пересчитывает их,
 * оставляя корректные записи нетронутыми.
 *
 * Использование:
 * - Пересчет ошибочных completion в курсе:
 *   php fix_invalid_completion.php --courseid=123
 *
 * - Пересчет для конкретных пользователей:
 *   php fix_invalid_completion.php --courseid=123 --userids=1,2,3
 *
 * - С предпросмотром:
 *   php fix_invalid_completion.php --courseid=123 --dry-run
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
        'dry-run' => false,
        'verbose' => false,
    ],
    [
        'h' => 'help',
        'c' => 'courseid',
        'u' => 'userids',
        'd' => 'dry-run',
        'v' => 'verbose',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
Скрипт для умного пересчета completion - обрабатывает только ошибочные записи.

Использование:
    php fix_invalid_completion.php [параметры]

Параметры:
    -h, --help              Показать эту справку
    -c, --courseid=ID       ID курса (обязательный)
    -u, --userids=IDS       Список ID пользователей через запятую (если не указано - все)
    -d, --dry-run           Показать что будет сделано, без фактических изменений
    -v, --verbose           Подробный вывод

Примеры:
    # Пересчет ошибочных completion в курсе
    php fix_invalid_completion.php --courseid=123

    # Пересчет для конкретных пользователей с предпросмотром
    php fix_invalid_completion.php --courseid=123 --userids=45,67,89 --dry-run -v

    # Применение исправлений
    php fix_invalid_completion.php --courseid=123 --userids=45,67,89 -v

Особенности:
    - Анализирует каждую completion запись
    - Сбрасывает ТОЛЬКО ошибочные записи
    - Корректные записи остаются нетронутыми
    - Автоматически пересчитывает после сброса

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
$isDryRun = $options['dry-run'];
$isVerbose = $options['verbose'];

// Получаем курс
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$completion_info = new completion_info($course);

cli_heading("Умный пересчет completion");
cli_writeln("Курс: {$course->fullname} (ID: {$course->id})");

if (!$completion_info->is_enabled()) {
    cli_error('Completion отключен для данного курса');
}

if ($isDryRun) {
    cli_heading('РЕЖИМ ПРОСМОТРА (DRY-RUN) - Изменения не будут применены', 2);
}

// Получаем список пользователей
if (!empty($options['userids'])) {
    $userids = array_map('intval', explode(',', $options['userids']));
    cli_writeln("Пользователи: " . implode(', ', $userids));
} else {
    $context = context_course::instance($course->id);
    $enrolled = get_enrolled_users($context, '', 0, 'u.id');
    $userids = array_keys($enrolled);
    cli_writeln("Все записанные пользователи: " . count($userids));
}

if (empty($userids)) {
    cli_error('Не найдено пользователей для обработки');
}

// Статистика
$stats = [
    'users_processed' => 0,
    'total_completions' => 0,
    'invalid_found' => 0,
    'invalid_fixed' => 0,
    'valid_kept' => 0,
    'errors' => 0,
];

// Получаем информацию о модулях курса
$modinfo = get_fast_modinfo($course);
$cms = $modinfo->get_cms();

cli_writeln("\nАнализ и исправление completion...\n");

// Обрабатываем каждого пользователя
foreach ($userids as $userid) {
    $user = $DB->get_record('user', ['id' => $userid]);
    
    if (!$user) {
        cli_problem("Пользователь ID {$userid} не найден");
        $stats['errors']++;
        continue;
    }
    
    $stats['users_processed']++;
    
    if ($isVerbose) {
        cli_writeln("Пользователь: {$user->username} ({$user->firstname} {$user->lastname}, ID: {$user->id})");
    }
    
    $user_invalid_count = 0;
    
    // Проверяем каждый модуль курса
    foreach ($cms as $cm) {
        if ($cm->completion == COMPLETION_TRACKING_NONE) {
            continue; // Пропускаем модули без отслеживания completion
        }
        
        // Получаем текущий статус completion
        $completion_data = $completion_info->get_data($cm, false, $userid);
        
        if ($completion_data->completionstate == COMPLETION_INCOMPLETE) {
            continue; // Пропускаем незавершенные
        }
        
        $stats['total_completions']++;
        
        // Проверяем соответствие критериям
        $issue = check_completion_validity($cm, $completion_data, $user, $completion_info);
        
        if ($issue) {
            // Найдена ошибочная запись!
            $stats['invalid_found']++;
            $user_invalid_count++;
            
            if ($isVerbose) {
                cli_writeln("  ❌ CM ID {$cm->id}: {$cm->name}");
                cli_writeln("     Проблема: {$issue['description']}");
                cli_writeln("     Действие: Сброс и пересчет");
            } elseif (!$isDryRun) {
                cli_writeln("Исправление: User {$userid} → CM {$cm->id} ({$cm->name})");
            }
            
            // Сбрасываем только эту ошибочную запись
            if (!$isDryRun) {
                try {
                    $DB->delete_records('course_modules_completion', [
                        'coursemoduleid' => $cm->id,
                        'userid' => $userid,
                    ]);
                    
                    // Запускаем пересчет для этого модуля и пользователя
                    $completion_info->update_state($cm, COMPLETION_UNKNOWN, $userid);
                    
                    $stats['invalid_fixed']++;
                    
                    if ($isVerbose) {
                        // Проверяем новый статус
                        $new_completion = $completion_info->get_data($cm, false, $userid);
                        $new_state = get_completion_state_name($new_completion->completionstate);
                        cli_writeln("     Новый статус: {$new_state}");
                    }
                } catch (Exception $e) {
                    cli_problem("     Ошибка: " . $e->getMessage());
                    $stats['errors']++;
                }
            }
        } else {
            // Запись корректна - оставляем как есть
            $stats['valid_kept']++;
            
            if ($isVerbose) {
                cli_writeln("  ✓ CM ID {$cm->id}: {$cm->name} - корректно");
            }
        }
    }
    
    if ($isVerbose) {
        if ($user_invalid_count > 0) {
            cli_writeln("  Итого ошибочных у пользователя: {$user_invalid_count}");
        } else {
            cli_writeln("  Все completion корректны");
        }
        cli_writeln("");
    }
}

// Выводим статистику
cli_writeln("\n" . str_repeat("═", 65));
cli_heading('Статистика выполнения:', 2);
cli_writeln("Пользователей обработано:       {$stats['users_processed']}");
cli_writeln("Всего completion записей:        {$stats['total_completions']}");
cli_writeln("Корректных (оставлено):          {$stats['valid_kept']}");
cli_writeln("Ошибочных (найдено):             {$stats['invalid_found']}");

if (!$isDryRun) {
    cli_writeln("Ошибочных (исправлено):          {$stats['invalid_fixed']}");
}

cli_writeln("Ошибок:                          {$stats['errors']}");

if ($isDryRun) {
    cli_writeln("\nЭто был режим просмотра. Для применения изменений запустите команду без --dry-run");
}

if (!$isDryRun && $stats['invalid_fixed'] > 0) {
    cli_writeln("\n✓ Ошибочные completion успешно исправлены!");
    cli_writeln("Корректные completion остались нетронутыми.");
    cli_writeln("Рекомендуется проверить результаты в интерфейсе Moodle.");
}

if ($stats['invalid_found'] == 0) {
    cli_writeln("\n✓ Ошибочных completion не обнаружено! Все записи корректны.");
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
    
    // 3. Проверка специфичных для модулей требований
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
            // Проверка требований форума
            $cm_record = $DB->get_record('course_modules', ['id' => $cm->id]);
            if ($cm_record && $cm_record->completionposts) {
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
    
    // 4. Проверка логической целостности статусов
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


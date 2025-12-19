<?php
/**
 * CLI скрипт для умного пересчета completion по категории курсов
 * 
 * Обрабатывает все курсы в категории и подкатегориях (рекурсивно),
 * находит только ОШИБОЧНЫЕ completion записи и пересчитывает их.
 *
 * Использование:
 * - Пересчет ошибочных completion во всех курсах категории:
 *   php fix_invalid_completion_by_category.php --categoryid=5
 *
 * - С предпросмотром:
 *   php fix_invalid_completion_by_category.php --categoryid=5 --dry-run
 *
 * - Только прямые курсы (без подкатегорий):
 *   php fix_invalid_completion_by_category.php --categoryid=5 --no-recursive
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

// Подавляем debugging notices в CLI режиме для чистого вывода
// Оставляем только ошибки и предупреждения
if (!defined('DEBUGGING_LEVEL')) {
    @error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
}

// Получаем параметры командной строки
list($options, $unrecognized) = cli_get_params(
    [
        'help' => false,
        'categoryid' => null,
        'userids' => null,
        'no-recursive' => false,
        'dry-run' => false,
        'verbose' => false,
    ],
    [
        'h' => 'help',
        'cat' => 'categoryid',
        'u' => 'userids',
        'nr' => 'no-recursive',
        'd' => 'dry-run',
        'v' => 'verbose',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
Скрипт для умного пересчета completion по категории курсов (рекурсивно).

Использование:
    php fix_invalid_completion_by_category.php [параметры]

Параметры:
    -h, --help              Показать эту справку
    --categoryid=ID, -cat   ID категории курсов (обязательный)
    -u, --userids=IDS       Список ID пользователей через запятую (если не указано - все)
    --no-recursive, -nr     Не обрабатывать подкатегории (только прямые курсы)
    -d, --dry-run           Показать что будет сделано, без фактических изменений
    -v, --verbose           Подробный вывод

Примеры:
    # Пересчет ошибочных completion во всех курсах категории и подкатегорий
    php fix_invalid_completion_by_category.php --categoryid=5

    # С предпросмотром
    php fix_invalid_completion_by_category.php --categoryid=5 --dry-run -v

    # Только прямые курсы категории (без подкатегорий)
    php fix_invalid_completion_by_category.php --categoryid=5 --no-recursive

    # Для конкретных пользователей
    php fix_invalid_completion_by_category.php --categoryid=5 --userids=45,67,89

Особенности:
    - Рекурсивно обрабатывает все подкатегории
    - Анализирует каждую completion запись
    - Сбрасывает ТОЛЬКО ошибочные записи
    - Корректные записи остаются нетронутыми
    - Показывает прогресс по каждому курсу

EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

// Валидация параметров
if (empty($options['categoryid'])) {
    cli_error('Необходимо указать --categoryid');
}

$categoryid = intval($options['categoryid']);
$userids = [];
$isRecursive = !$options['no-recursive'];
$isDryRun = $options['dry-run'];
$isVerbose = $options['verbose'];

// Получаем категорию
try {
    $category = core_course_category::get($categoryid);
} catch (Exception $e) {
    cli_error("Категория ID {$categoryid} не найдена: " . $e->getMessage());
}

cli_heading("Умный пересчет completion по категории");
cli_writeln("Категория: {$category->name} (ID: {$category->id})");
cli_writeln("Рекурсивно: " . ($isRecursive ? 'Да (включая подкатегории)' : 'Нет (только прямые курсы)'));

if ($isDryRun) {
    cli_heading('РЕЖИМ ПРОСМОТРА (DRY-RUN) - Изменения не будут применены', 2);
}

// Получаем список ID пользователей
if (!empty($options['userids'])) {
    $userids = array_map('intval', explode(',', $options['userids']));
    cli_writeln("Обработка пользователей: " . implode(', ', $userids));
}

cli_writeln("\n" . str_repeat("═", 80));

// Получаем все курсы из категории (рекурсивно или нет)
cli_writeln("\nПоиск курсов...");

if ($isRecursive) {
    $courses = get_courses_recursive($category);
} else {
    // Получаем курсы только из текущей категории
    $course_elements = $category->get_courses(['recursive' => false, 'coursecontacts' => false]);
    $courses = [];
    foreach ($course_elements as $course_element) {
        // Получаем полный объект курса через get_course() (возвращает stdClass)
        try {
            $course = get_course($course_element->id);
            $courses[] = $course;
        } catch (Exception $e) {
            // Пропускаем недоступные курсы
            continue;
        }
    }
}

$course_count = count($courses);

if ($course_count == 0) {
    cli_writeln("В категории не найдено курсов.");
    exit(0);
}

cli_writeln("Найдено курсов: {$course_count}");
cli_writeln("");

// Общая статистика
$global_stats = [
    'courses_total' => $course_count,
    'courses_processed' => 0,
    'courses_with_completion' => 0,
    'courses_skipped' => 0,
    'users_processed' => 0,
    'total_completions' => 0,
    'invalid_found' => 0,
    'invalid_fixed' => 0,
    'valid_kept' => 0,
    'errors' => 0,
];

// Обрабатываем каждый курс
$course_number = 0;
foreach ($courses as $course) {
    $course_number++;
    
    cli_writeln(str_repeat("─", 80));
    cli_writeln("Курс {$course_number}/{$course_count}: {$course->fullname} (ID: {$course->id})");
    
    try {
        // Проверяем что completion включен
        $completion_info = new completion_info($course);
        
        if (!$completion_info->is_enabled()) {
            cli_writeln("  ⊗ Completion отключен, пропускаем");
            $global_stats['courses_skipped']++;
            continue;
        }
        
        $global_stats['courses_with_completion']++;
        
        // Получаем список пользователей для этого курса
        $course_userids = $userids;
        if (empty($course_userids)) {
            $context = context_course::instance($course->id);
            $enrolled = get_enrolled_users($context, '', 0, 'u.id');
            $course_userids = array_keys($enrolled);
        }
        
        if (empty($course_userids)) {
            cli_writeln("  ⊗ Нет записанных пользователей");
            $global_stats['courses_skipped']++;
            continue;
        }
        
        cli_writeln("  Пользователей: " . count($course_userids));
        
        // Обрабатываем курс
        $course_stats = process_course_completion(
            $course,
            $course_userids,
            $completion_info,
            $isDryRun,
            $isVerbose
        );
        
        // Обновляем глобальную статистику
        $global_stats['courses_processed']++;
        $global_stats['users_processed'] += $course_stats['users_processed'];
        $global_stats['total_completions'] += $course_stats['total_completions'];
        $global_stats['invalid_found'] += $course_stats['invalid_found'];
        $global_stats['invalid_fixed'] += $course_stats['invalid_fixed'];
        $global_stats['valid_kept'] += $course_stats['valid_kept'];
        $global_stats['errors'] += $course_stats['errors'];
        
        // Краткая статистика по курсу
        if ($course_stats['invalid_found'] > 0) {
            cli_writeln("  ✓ Найдено ошибочных: {$course_stats['invalid_found']}");
            if (!$isDryRun) {
                cli_writeln("  ✓ Исправлено: {$course_stats['invalid_fixed']}");
            }
        } else {
            cli_writeln("  ✓ Ошибочных completion не найдено");
        }
        
    } catch (Exception $e) {
        cli_problem("  Ошибка при обработке курса: " . $e->getMessage());
        $global_stats['errors']++;
    }
}

// Выводим глобальную статистику
cli_writeln("\n" . str_repeat("═", 80));
cli_heading('ОБЩАЯ СТАТИСТИКА ПО ВСЕМ КУРСАМ:', 2);
cli_writeln("Категория: {$category->name}");
cli_writeln("");
cli_writeln("Курсов:");
cli_writeln("  Всего найдено:                  {$global_stats['courses_total']}");
cli_writeln("  С включенным completion:        {$global_stats['courses_with_completion']}");
cli_writeln("  Обработано:                     {$global_stats['courses_processed']}");
cli_writeln("  Пропущено:                      {$global_stats['courses_skipped']}");
cli_writeln("");
cli_writeln("Completion записи:");
cli_writeln("  Пользователей обработано:       {$global_stats['users_processed']}");
cli_writeln("  Всего completion записей:       {$global_stats['total_completions']}");
cli_writeln("  Корректных (оставлено):         {$global_stats['valid_kept']}");
cli_writeln("  Ошибочных (найдено):            {$global_stats['invalid_found']}");

if (!$isDryRun) {
    cli_writeln("  Ошибочных (исправлено):         {$global_stats['invalid_fixed']}");
}

cli_writeln("  Ошибок:                         {$global_stats['errors']}");

if ($isDryRun) {
    cli_writeln("\nЭто был режим просмотра. Для применения изменений запустите команду без --dry-run");
}

if (!$isDryRun && $global_stats['invalid_fixed'] > 0) {
    cli_writeln("\n✓ Ошибочные completion успешно исправлены во всех курсах!");
    cli_writeln("Корректные completion остались нетронутыми.");
    cli_writeln("Рекомендуется выборочно проверить результаты в интерфейсе Moodle.");
}

if ($global_stats['invalid_found'] == 0) {
    cli_writeln("\n✓ Ошибочных completion не обнаружено! Все записи корректны.");
}

cli_writeln("\nГотово!");
exit(0);

// ========== Вспомогательные функции ==========

/**
 * Получает все курсы из категории рекурсивно
 *
 * @param core_course_category $category Категория
 * @return array Массив курсов (stdClass)
 */
function get_courses_recursive($category) {
    $courses = [];
    
    // Получаем ID курсов из текущей категории
    $current_courses = $category->get_courses(['recursive' => false, 'coursecontacts' => false]);
    foreach ($current_courses as $course_element) {
        // Получаем полный объект курса через get_course() (возвращает stdClass)
        try {
            $course = get_course($course_element->id);
            $courses[] = $course;
        } catch (Exception $e) {
            // Пропускаем недоступные курсы
            continue;
        }
    }
    
    // Получаем подкатегории
    $children = $category->get_children();
    foreach ($children as $child) {
        $child_courses = get_courses_recursive($child);
        $courses = array_merge($courses, $child_courses);
    }
    
    return $courses;
}

/**
 * Обрабатывает completion в курсе
 *
 * @param stdClass $course Курс
 * @param array $userids Список ID пользователей
 * @param completion_info $completion_info Объект completion
 * @param bool $isDryRun Режим просмотра
 * @param bool $isVerbose Подробный вывод
 * @return array Статистика обработки
 */
function process_course_completion($course, $userids, $completion_info, $isDryRun, $isVerbose) {
    global $DB;
    
    $stats = [
        'users_processed' => 0,
        'total_completions' => 0,
        'invalid_found' => 0,
        'invalid_fixed' => 0,
        'valid_kept' => 0,
        'errors' => 0,
    ];
    
    // Получаем информацию о модулях курса
    try {
        $modinfo = get_fast_modinfo($course);
        $cms = $modinfo->get_cms();
    } catch (Exception $e) {
        if ($isVerbose) {
            cli_problem("    Ошибка при получении информации о модулях: " . $e->getMessage());
        }
        $stats['errors']++;
        return $stats;
    }
    
    // Обрабатываем каждого пользователя
    foreach ($userids as $userid) {
        $user = $DB->get_record('user', ['id' => $userid]);
        
        if (!$user) {
            if ($isVerbose) {
                cli_problem("    Пользователь ID {$userid} не найден");
            }
            $stats['errors']++;
            continue;
        }
        
        $stats['users_processed']++;
        
        $user_invalid_count = 0;
        
        // Проверяем каждый модуль курса
        foreach ($cms as $cm) {
            if ($cm->completion == COMPLETION_TRACKING_NONE) {
                continue;
            }
            
            // Получаем текущий статус completion
            $completion_data = $completion_info->get_data($cm, false, $userid);
            
            if ($completion_data->completionstate == COMPLETION_INCOMPLETE) {
                continue;
            }
            
            $stats['total_completions']++;
            
            // Проверяем соответствие критериям
            $issue = check_completion_validity($cm, $completion_data, $user, $completion_info);
            
            if ($issue) {
                // Найдена ошибочная запись!
                $stats['invalid_found']++;
                $user_invalid_count++;
                
                if ($isVerbose) {
                    cli_writeln("    ❌ User {$userid} → CM {$cm->id}: {$cm->name}");
                    cli_writeln("       Проблема: {$issue['description']}");
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
                        
                    } catch (Exception $e) {
                        if ($isVerbose) {
                            cli_problem("       Ошибка: " . $e->getMessage());
                        }
                        $stats['errors']++;
                    }
                }
            } else {
                // Запись корректна
                $stats['valid_kept']++;
            }
        }
    }
    
    return $stats;
}

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
                            'description' => sprintf('Оценка %.2f < проходной %.2f', $user_grade_value, $grade_item->gradepass),
                        ];
                    }
                }
            }
        }
    }
    
    // 3. Проверка специфичных для модулей требований
    switch ($cm->modname) {
        case 'assign':
            $submission = $DB->get_record('assign_submission', [
                'assignment' => $cm->instance,
                'userid' => $user->id,
                'status' => 'submitted',
            ]);
            
            if (!$submission && $completion_data->completionstate != COMPLETION_INCOMPLETE) {
                return [
                    'type' => 'No Submission',
                    'description' => 'Нет отправленной работы',
                ];
            }
            break;
            
        case 'quiz':
            $attempts = $DB->get_records('quiz_attempts', [
                'quiz' => $cm->instance,
                'userid' => $user->id,
                'state' => 'finished',
            ]);
            
            if (empty($attempts) && $completion_data->completionstate != COMPLETION_INCOMPLETE) {
                return [
                    'type' => 'No Quiz Attempts',
                    'description' => 'Нет завершенных попыток теста',
                ];
            }
            break;
            
        case 'forum':
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
                        'description' => sprintf('Требуется %d сообщений, создано %d', $cm_record->completionposts, $posts_count),
                    ];
                }
            }
            break;
    }
    
    // 4. Проверка логической целостности статусов
    if ($completion_data->completionstate == COMPLETION_COMPLETE_PASS ||
        $completion_data->completionstate == COMPLETION_COMPLETE_FAIL) {
        if ($cm->completiongradeitemnumber === null) {
            return [
                'type' => 'Invalid Pass/Fail State',
                'description' => 'Некорректный статус сдачи',
            ];
        }
    }
    
    return null;
}


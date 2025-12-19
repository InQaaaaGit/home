<?php
/**
 * CLI скрипт для сброса и пересчета статусов completion пользователей
 *
 * Использование:
 * - Сбросить completion для конкретных пользователей в курсе:
 *   php reset_completion.php --courseid=123 --userids=1,2,3
 *
 * - Сбросить completion для всех пользователей в курсе:
 *   php reset_completion.php --courseid=123 --all
 *
 * - Сбросить completion для пользователей в конкретном элементе курса:
 *   php reset_completion.php --cmid=456 --userids=1,2,3
 *
 * - Пересчитать completion после сброса:
 *   php reset_completion.php --courseid=123 --userids=1,2,3 --recalculate
 *
 * - Просмотр текущих данных без изменений (dry-run):
 *   php reset_completion.php --courseid=123 --userids=1,2,3 --dry-run
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
        'cmid' => null,
        'userids' => null,
        'all' => false,
        'recalculate' => false,
        'dry-run' => false,
        'verbose' => false,
    ],
    [
        'h' => 'help',
        'c' => 'courseid',
        'm' => 'cmid',
        'u' => 'userids',
        'a' => 'all',
        'r' => 'recalculate',
        'd' => 'dry-run',
        'v' => 'verbose',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
Скрипт для сброса и пересчета статусов completion пользователей в Moodle.

Использование:
    php reset_completion.php [параметры]

Параметры:
    -h, --help              Показать эту справку
    -c, --courseid=ID       ID курса для обработки
    -m, --cmid=ID           ID элемента курса (course module) для обработки
    -u, --userids=IDS       Список ID пользователей через запятую (например: 1,2,3)
    -a, --all               Обработать всех пользователей курса
    -r, --recalculate       Пересчитать completion после сброса
    -d, --dry-run           Показать что будет сделано, без фактических изменений
    -v, --verbose           Подробный вывод

Примеры:
    # Сбросить completion для пользователей 1,2,3 в курсе 123
    php reset_completion.php --courseid=123 --userids=1,2,3

    # Сбросить completion для всех пользователей в курсе 123 и пересчитать
    php reset_completion.php --courseid=123 --all --recalculate

    # Просмотр данных без изменений
    php reset_completion.php --courseid=123 --userids=1,2,3 --dry-run --verbose

    # Сбросить completion для конкретного элемента курса
    php reset_completion.php --cmid=456 --userids=1,2,3

EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

// Валидация параметров
if (empty($options['courseid']) && empty($options['cmid'])) {
    cli_error('Необходимо указать --courseid или --cmid');
}

if (empty($options['userids']) && !$options['all']) {
    cli_error('Необходимо указать --userids или --all');
}

$courseid = $options['courseid'] ? intval($options['courseid']) : null;
$cmid = $options['cmid'] ? intval($options['cmid']) : null;
$userids = [];
$isDryRun = $options['dry-run'];
$isVerbose = $options['verbose'];
$recalculate = $options['recalculate'];

// Получаем курс
$course = null;
if ($courseid) {
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    cli_heading("Обработка курса: {$course->fullname} (ID: {$course->id})");
} elseif ($cmid) {
    $cm = $DB->get_record('course_modules', ['id' => $cmid], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    cli_heading("Обработка элемента курса ID: {$cmid} в курсе: {$course->fullname}");
}

// Получаем список пользователей
if ($options['all']) {
    $context = context_course::instance($course->id);
    $enrolled = get_enrolled_users($context, '', 0, 'u.id, u.username, u.firstname, u.lastname');
    $userids = array_keys($enrolled);
    cli_writeln("Найдено записанных пользователей: " . count($userids));
} else {
    $userids = array_map('intval', explode(',', $options['userids']));
    cli_writeln("Обработка пользователей: " . implode(', ', $userids));
}

if (empty($userids)) {
    cli_error('Не найдено пользователей для обработки');
}

if ($isDryRun) {
    cli_heading('РЕЖИМ ПРОСМОТРА (DRY-RUN) - Изменения не будут применены', 2);
}

// Статистика
$stats = [
    'users_processed' => 0,
    'completions_found' => 0,
    'completions_reset' => 0,
    'errors' => 0,
];

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
        cli_writeln("\nОбработка пользователя: {$user->username} ({$user->firstname} {$user->lastname}, ID: {$user->id})");
    }
    
    try {
        if ($cmid) {
            // Сброс completion для конкретного элемента курса
            reset_cm_completion($cmid, $userid, $isDryRun, $isVerbose, $stats);
        } else {
            // Сброс completion для всех элементов курса
            reset_course_completion($course->id, $userid, $isDryRun, $isVerbose, $stats);
        }
        
        // Пересчитываем completion если требуется
        if ($recalculate && !$isDryRun) {
            recalculate_user_completion($course->id, $userid, $isVerbose);
        }
        
    } catch (Exception $e) {
        cli_problem("Ошибка при обработке пользователя ID {$userid}: " . $e->getMessage());
        $stats['errors']++;
    }
}

// Выводим статистику
cli_heading('Статистика выполнения:', 2);
cli_writeln("Пользователей обработано: {$stats['users_processed']}");
cli_writeln("Записей completion найдено: {$stats['completions_found']}");
cli_writeln("Записей completion сброшено: {$stats['completions_reset']}");
cli_writeln("Ошибок: {$stats['errors']}");

if ($isDryRun) {
    cli_writeln("\nЭто был режим просмотра. Для применения изменений запустите команду без --dry-run");
}

cli_writeln("\nГотово!");
exit(0);

// ========== Вспомогательные функции ==========

/**
 * Сбрасывает completion для конкретного элемента курса
 *
 * @param int $cmid ID элемента курса
 * @param int $userid ID пользователя
 * @param bool $isDryRun Режим просмотра
 * @param bool $isVerbose Подробный вывод
 * @param array &$stats Статистика
 * @throws dml_exception
 */
function reset_cm_completion($cmid, $userid, $isDryRun, $isVerbose, &$stats) {
    global $DB;
    
    $completion = $DB->get_record('course_modules_completion', [
        'coursemoduleid' => $cmid,
        'userid' => $userid,
    ]);
    
    if ($completion) {
        $stats['completions_found']++;
        
        if ($isVerbose) {
            $state_name = get_completion_state_name($completion->completionstate);
            cli_writeln("  - Найден completion для CM ID {$cmid}: статус={$state_name}, " .
                       "просмотрено=" . ($completion->viewed ? 'Да' : 'Нет') . ", " .
                       "время=" . userdate($completion->timemodified));
        }
        
        if (!$isDryRun) {
            $DB->delete_records('course_modules_completion', ['id' => $completion->id]);
            $stats['completions_reset']++;
            
            if ($isVerbose) {
                cli_writeln("  ✓ Completion сброшен");
            }
        }
    } else {
        if ($isVerbose) {
            cli_writeln("  - Completion не найден для CM ID {$cmid}");
        }
    }
}

/**
 * Сбрасывает completion для всех элементов курса пользователя
 *
 * @param int $courseid ID курса
 * @param int $userid ID пользователя
 * @param bool $isDryRun Режим просмотра
 * @param bool $isVerbose Подробный вывод
 * @param array &$stats Статистика
 * @throws dml_exception
 */
function reset_course_completion($courseid, $userid, $isDryRun, $isVerbose, &$stats) {
    global $DB;
    
    // Получаем все записи completion для пользователя в курсе
    $sql = "SELECT cmc.*
            FROM {course_modules_completion} cmc
            JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
            WHERE cm.course = :courseid AND cmc.userid = :userid";
    
    $completions = $DB->get_records_sql($sql, [
        'courseid' => $courseid,
        'userid' => $userid,
    ]);
    
    if (!empty($completions)) {
        $stats['completions_found'] += count($completions);
        
        if ($isVerbose) {
            cli_writeln("  Найдено completion записей: " . count($completions));
        }
        
        foreach ($completions as $completion) {
            if ($isVerbose) {
                $state_name = get_completion_state_name($completion->completionstate);
                cli_writeln("    - CM ID {$completion->coursemoduleid}: статус={$state_name}");
            }
            
            if (!$isDryRun) {
                $DB->delete_records('course_modules_completion', ['id' => $completion->id]);
                $stats['completions_reset']++;
            }
        }
        
        if (!$isDryRun) {
            // Также сбрасываем completion курса целиком
            $course_completion = $DB->get_record('course_completions', [
                'course' => $courseid,
                'userid' => $userid,
            ]);
            
            if ($course_completion) {
                $DB->delete_records('course_completions', ['id' => $course_completion->id]);
                
                if ($isVerbose) {
                    cli_writeln("  ✓ Сброшен completion курса");
                }
            }
            
            if ($isVerbose) {
                cli_writeln("  ✓ Все completion записи сброшены");
            }
        }
    } else {
        if ($isVerbose) {
            cli_writeln("  - Completion записи не найдены");
        }
    }
}

/**
 * Пересчитывает completion для пользователя
 *
 * @param int $courseid ID курса
 * @param int $userid ID пользователя
 * @param bool $isVerbose Подробный вывод
 */
function recalculate_user_completion($courseid, $userid, $isVerbose) {
    global $DB;
    
    if ($isVerbose) {
        cli_writeln("  Пересчет completion...");
    }
    
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    $completion_info = new completion_info($course);
    
    if (!$completion_info->is_enabled()) {
        if ($isVerbose) {
            cli_writeln("  ! Completion отключен для этого курса");
        }
        return;
    }
    
    // Получаем все модули курса с включенным completion
    $modinfo = get_fast_modinfo($course);
    $cms = $modinfo->get_cms();
    
    $recalculated = 0;
    foreach ($cms as $cm) {
        if ($cm->completion != COMPLETION_TRACKING_NONE) {
            $completion_data = $completion_info->get_data($cm, false, $userid);
            
            // Проверяем, должен ли быть засчитан completion
            $completion_info->update_state($cm, COMPLETION_UNKNOWN, $userid);
            $recalculated++;
        }
    }
    
    if ($isVerbose) {
        cli_writeln("  ✓ Пересчитано элементов: {$recalculated}");
    }
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


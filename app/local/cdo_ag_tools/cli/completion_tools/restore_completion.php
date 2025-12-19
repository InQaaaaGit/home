<?php
/**
 * CLI скрипт для восстановления данных completion из backup
 *
 * Восстанавливает данные completion из JSON backup, созданного скриптом backup_completion.php
 *
 * Использование:
 *   php restore_completion.php --input=/tmp/completion_backup.json
 *
 * @package    local_cdo_ag_tools
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// Получаем параметры командной строки
list($options, $unrecognized) = cli_get_params(
    [
        'help' => false,
        'input' => null,
        'dry-run' => false,
        'force' => false,
        'verbose' => false,
    ],
    [
        'h' => 'help',
        'i' => 'input',
        'd' => 'dry-run',
        'f' => 'force',
        'v' => 'verbose',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
Скрипт для восстановления данных completion из backup.

Использование:
    php restore_completion.php [параметры]

Параметры:
    -h, --help              Показать эту справку
    -i, --input=FILE        Путь к файлу backup (обязательный)
    -d, --dry-run           Показать что будет сделано, без фактических изменений
    -f, --force             Перезаписать существующие записи
    -v, --verbose           Подробный вывод

Примеры:
    # Восстановление с проверкой (dry-run)
    php restore_completion.php --input=/tmp/completion_backup.json --dry-run

    # Восстановление с применением изменений
    php restore_completion.php --input=/tmp/completion_backup.json

    # Восстановление с перезаписью существующих записей
    php restore_completion.php --input=/tmp/completion_backup.json --force

EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

// Валидация параметров
if (empty($options['input'])) {
    cli_error('Необходимо указать --input с путем к файлу backup');
}

$input_file = $options['input'];
$isDryRun = $options['dry-run'];
$isForce = $options['force'];
$isVerbose = $options['verbose'];

if (!file_exists($input_file)) {
    cli_error("Файл не найден: {$input_file}");
}

if (!is_readable($input_file)) {
    cli_error("Файл не доступен для чтения: {$input_file}");
}

cli_heading("Восстановление completion данных");

if ($isDryRun) {
    cli_heading('РЕЖИМ ПРОСМОТРА (DRY-RUN) - Изменения не будут применены', 2);
}

// Загружаем данные backup
cli_writeln("Загрузка backup из: {$input_file}");

$json_content = file_get_contents($input_file);
if ($json_content === false) {
    cli_error("Не удалось прочитать файл: {$input_file}");
}

$backup_data = json_decode($json_content, true);
if ($backup_data === null) {
    cli_error("Ошибка декодирования JSON: " . json_last_error_msg());
}

// Проверяем структуру backup
if (!isset($backup_data['backup_info']) ||
    !isset($backup_data['course_modules_completion']) ||
    !isset($backup_data['course_completions'])) {
    cli_error("Некорректная структура backup файла");
}

// Выводим информацию о backup
$info = $backup_data['backup_info'];
cli_writeln("\nИнформация о backup:");
cli_writeln("  Создан:          " . $info['created_readable']);
cli_writeln("  Курс ID:         " . $info['course_id']);
cli_writeln("  Курс:            " . $info['course_name']);
cli_writeln("  Moodle версия:   " . $info['moodle_version']);

$cm_count = count($backup_data['course_modules_completion']);
$cc_count = count($backup_data['course_completions']);

cli_writeln("\nДанные для восстановления:");
cli_writeln("  course_modules_completion: {$cm_count} записей");
cli_writeln("  course_completions:        {$cc_count} записей");

// Проверяем что курс существует
$course = $DB->get_record('course', ['id' => $info['course_id']]);
if (!$course) {
    cli_error("Курс с ID {$info['course_id']} не найден в текущей базе данных");
}

cli_writeln("\n" . str_repeat("─", 65));

// Статистика
$stats = [
    'cm_completion_inserted' => 0,
    'cm_completion_updated' => 0,
    'cm_completion_skipped' => 0,
    'cc_inserted' => 0,
    'cc_updated' => 0,
    'cc_skipped' => 0,
    'errors' => 0,
];

// Восстанавливаем course_modules_completion
if (!empty($backup_data['course_modules_completion'])) {
    cli_writeln("\nВосстановление course_modules_completion...");
    
    foreach ($backup_data['course_modules_completion'] as $record) {
        try {
            $existing = $DB->get_record('course_modules_completion', [
                'coursemoduleid' => $record['coursemoduleid'],
                'userid' => $record['userid'],
            ]);
            
            if ($existing) {
                if ($isForce) {
                    if ($isVerbose) {
                        cli_writeln("  Обновление: CM {$record['coursemoduleid']}, User {$record['userid']}");
                    }
                    
                    if (!$isDryRun) {
                        $record['id'] = $existing->id;
                        $DB->update_record('course_modules_completion', (object)$record);
                    }
                    
                    $stats['cm_completion_updated']++;
                } else {
                    if ($isVerbose) {
                        cli_writeln("  Пропуск (уже существует): CM {$record['coursemoduleid']}, User {$record['userid']}");
                    }
                    $stats['cm_completion_skipped']++;
                }
            } else {
                if ($isVerbose) {
                    cli_writeln("  Вставка: CM {$record['coursemoduleid']}, User {$record['userid']}");
                }
                
                if (!$isDryRun) {
                    // Проверяем что coursemodule существует
                    $cm_exists = $DB->record_exists('course_modules', ['id' => $record['coursemoduleid']]);
                    if (!$cm_exists) {
                        cli_problem("  ПРЕДУПРЕЖДЕНИЕ: Course module ID {$record['coursemoduleid']} не существует, запись пропущена");
                        $stats['cm_completion_skipped']++;
                        continue;
                    }
                    
                    // Проверяем что пользователь существует
                    $user_exists = $DB->record_exists('user', ['id' => $record['userid']]);
                    if (!$user_exists) {
                        cli_problem("  ПРЕДУПРЕЖДЕНИЕ: Пользователь ID {$record['userid']} не существует, запись пропущена");
                        $stats['cm_completion_skipped']++;
                        continue;
                    }
                    
                    unset($record['id']); // Удаляем ID для вставки новой записи
                    $DB->insert_record('course_modules_completion', (object)$record);
                }
                
                $stats['cm_completion_inserted']++;
            }
        } catch (Exception $e) {
            cli_problem("  Ошибка при обработке записи: " . $e->getMessage());
            $stats['errors']++;
        }
    }
}

// Восстанавливаем course_completions
if (!empty($backup_data['course_completions'])) {
    cli_writeln("\nВосстановление course_completions...");
    
    foreach ($backup_data['course_completions'] as $record) {
        try {
            $existing = $DB->get_record('course_completions', [
                'course' => $record['course'],
                'userid' => $record['userid'],
            ]);
            
            if ($existing) {
                if ($isForce) {
                    if ($isVerbose) {
                        cli_writeln("  Обновление: Course {$record['course']}, User {$record['userid']}");
                    }
                    
                    if (!$isDryRun) {
                        $record['id'] = $existing->id;
                        $DB->update_record('course_completions', (object)$record);
                    }
                    
                    $stats['cc_updated']++;
                } else {
                    if ($isVerbose) {
                        cli_writeln("  Пропуск (уже существует): Course {$record['course']}, User {$record['userid']}");
                    }
                    $stats['cc_skipped']++;
                }
            } else {
                if ($isVerbose) {
                    cli_writeln("  Вставка: Course {$record['course']}, User {$record['userid']}");
                }
                
                if (!$isDryRun) {
                    // Проверяем что курс существует
                    $course_exists = $DB->record_exists('course', ['id' => $record['course']]);
                    if (!$course_exists) {
                        cli_problem("  ПРЕДУПРЕЖДЕНИЕ: Курс ID {$record['course']} не существует, запись пропущена");
                        $stats['cc_skipped']++;
                        continue;
                    }
                    
                    // Проверяем что пользователь существует
                    $user_exists = $DB->record_exists('user', ['id' => $record['userid']]);
                    if (!$user_exists) {
                        cli_problem("  ПРЕДУПРЕЖДЕНИЕ: Пользователь ID {$record['userid']} не существует, запись пропущена");
                        $stats['cc_skipped']++;
                        continue;
                    }
                    
                    unset($record['id']); // Удаляем ID для вставки новой записи
                    $DB->insert_record('course_completions', (object)$record);
                }
                
                $stats['cc_inserted']++;
            }
        } catch (Exception $e) {
            cli_problem("  Ошибка при обработке записи: " . $e->getMessage());
            $stats['errors']++;
        }
    }
}

// Выводим статистику
cli_writeln("\n" . str_repeat("═", 65));
cli_heading('Статистика восстановления:', 2);

cli_writeln("course_modules_completion:");
cli_writeln("  Вставлено:     {$stats['cm_completion_inserted']}");
cli_writeln("  Обновлено:     {$stats['cm_completion_updated']}");
cli_writeln("  Пропущено:     {$stats['cm_completion_skipped']}");

cli_writeln("\ncourse_completions:");
cli_writeln("  Вставлено:     {$stats['cc_inserted']}");
cli_writeln("  Обновлено:     {$stats['cc_updated']}");
cli_writeln("  Пропущено:     {$stats['cc_skipped']}");

cli_writeln("\nОшибок:          {$stats['errors']}");

if ($isDryRun) {
    cli_writeln("\nЭто был режим просмотра. Для применения изменений запустите команду без --dry-run");
    cli_writeln("Для перезаписи существующих записей добавьте --force");
}

if (!$isDryRun && ($stats['cm_completion_inserted'] > 0 || $stats['cm_completion_updated'] > 0 ||
    $stats['cc_inserted'] > 0 || $stats['cc_updated'] > 0)) {
    cli_writeln("\n✓ Данные успешно восстановлены!");
    cli_writeln("Рекомендуется проверить результаты в интерфейсе Moodle.");
}

cli_writeln("\nГотово!");
exit(0);


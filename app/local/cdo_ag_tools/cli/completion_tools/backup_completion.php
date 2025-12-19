<?php
/**
 * CLI скрипт для резервного копирования данных completion перед их изменением
 *
 * Создает JSON backup данных completion для возможности восстановления
 *
 * Использование:
 *   php backup_completion.php --courseid=123 --output=/tmp/completion_backup.json
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
        'courseid' => null,
        'userids' => null,
        'output' => null,
        'format' => 'json',
    ],
    [
        'h' => 'help',
        'c' => 'courseid',
        'u' => 'userids',
        'o' => 'output',
        'f' => 'format',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
Скрипт для резервного копирования данных completion.

Использование:
    php backup_completion.php [параметры]

Параметры:
    -h, --help              Показать эту справку
    -c, --courseid=ID       ID курса для backup (обязательный)
    -u, --userids=IDS       Список ID пользователей (если не указано - все)
    -o, --output=FILE       Путь к файлу backup (по умолчанию: completion_backup_TIMESTAMP.json)
    -f, --format=FORMAT     Формат backup: json или sql (по умолчанию: json)

Примеры:
    # Backup всех completion в курсе
    php backup_completion.php --courseid=123

    # Backup completion конкретных пользователей
    php backup_completion.php --courseid=123 --userids=45,67,89

    # Backup с указанием файла
    php backup_completion.php --courseid=123 --output=/tmp/my_backup.json

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
$format = $options['format'];

if (!in_array($format, ['json', 'sql'])) {
    cli_error('Неподдерживаемый формат. Используйте: json или sql');
}

// Получаем курс
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

cli_heading("Резервное копирование completion данных");
cli_writeln("Курс: {$course->fullname} (ID: {$course->id})");

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

// Формируем имя файла
$timestamp = date('Y-m-d_H-i-s');
if (empty($options['output'])) {
    $output_file = "/tmp/completion_backup_course{$courseid}_{$timestamp}.{$format}";
} else {
    $output_file = $options['output'];
}

cli_writeln("Файл backup: {$output_file}");
cli_writeln("");

// Собираем данные completion
cli_writeln("Сбор данных completion...");

$backup_data = [
    'backup_info' => [
        'created' => time(),
        'created_readable' => userdate(time()),
        'course_id' => $courseid,
        'course_name' => $course->fullname,
        'moodle_version' => $CFG->version,
        'format' => $format,
    ],
    'course_modules_completion' => [],
    'course_completions' => [],
];

// 1. Собираем course_modules_completion
$sql = "SELECT cmc.*
        FROM {course_modules_completion} cmc
        JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
        WHERE cm.course = :courseid";

$params = ['courseid' => $courseid];

if (!empty($userids)) {
    list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
    $sql .= " AND cmc.userid {$insql}";
    $params = array_merge($params, $inparams);
}

$cm_completions = $DB->get_records_sql($sql, $params);
cli_writeln("Найдено записей course_modules_completion: " . count($cm_completions));

foreach ($cm_completions as $completion) {
    $backup_data['course_modules_completion'][] = (array)$completion;
}

// 2. Собираем course_completions
$sql = "SELECT *
        FROM {course_completions}
        WHERE course = :courseid";

$params = ['courseid' => $courseid];

if (!empty($userids)) {
    list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
    $sql .= " AND userid {$insql}";
    $params = array_merge($params, $inparams);
}

$course_completions = $DB->get_records_sql($sql, $params);
cli_writeln("Найдено записей course_completions: " . count($course_completions));

foreach ($course_completions as $completion) {
    $backup_data['course_completions'][] = (array)$completion;
}

// Сохраняем backup
cli_writeln("\nСохранение backup...");

if ($format === 'json') {
    $json = json_encode($backup_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($output_file, $json) === false) {
        cli_error("Не удалось записать файл {$output_file}");
    }
} elseif ($format === 'sql') {
    generate_sql_backup($backup_data, $output_file);
}

cli_writeln("✓ Backup успешно создан: {$output_file}");
cli_writeln("\nСтатистика:");
cli_writeln("  - Записей course_modules_completion: " . count($backup_data['course_modules_completion']));
cli_writeln("  - Записей course_completions: " . count($backup_data['course_completions']));

$filesize = filesize($output_file);
$filesize_readable = format_filesize($filesize);
cli_writeln("  - Размер файла: {$filesize_readable}");

cli_writeln("\nДля восстановления используйте:");
cli_writeln("  php restore_completion.php --input={$output_file}");

cli_writeln("\nГотово!");
exit(0);

// ========== Вспомогательные функции ==========

/**
 * Генерирует SQL backup
 *
 * @param array $backup_data Данные для backup
 * @param string $output_file Путь к файлу
 */
function generate_sql_backup($backup_data, $output_file) {
    global $DB;
    
    $sql_content = "-- Completion Backup\n";
    $sql_content .= "-- Created: " . $backup_data['backup_info']['created_readable'] . "\n";
    $sql_content .= "-- Course ID: " . $backup_data['backup_info']['course_id'] . "\n";
    $sql_content .= "-- Course: " . $backup_data['backup_info']['course_name'] . "\n\n";
    
    // course_modules_completion
    if (!empty($backup_data['course_modules_completion'])) {
        $sql_content .= "-- course_modules_completion\n";
        foreach ($backup_data['course_modules_completion'] as $record) {
            $sql_content .= generate_insert_statement('course_modules_completion', $record);
        }
        $sql_content .= "\n";
    }
    
    // course_completions
    if (!empty($backup_data['course_completions'])) {
        $sql_content .= "-- course_completions\n";
        foreach ($backup_data['course_completions'] as $record) {
            $sql_content .= generate_insert_statement('course_completions', $record);
        }
    }
    
    if (file_put_contents($output_file, $sql_content) === false) {
        cli_error("Не удалось записать файл {$output_file}");
    }
}

/**
 * Генерирует INSERT statement для записи
 *
 * @param string $table Название таблицы
 * @param array $record Запись
 * @return string SQL statement
 */
function generate_insert_statement($table, $record) {
    global $CFG;
    
    $table_name = $CFG->prefix . $table;
    $fields = array_keys($record);
    $values = [];
    
    foreach ($record as $value) {
        if ($value === null) {
            $values[] = 'NULL';
        } elseif (is_numeric($value)) {
            $values[] = $value;
        } else {
            $values[] = "'" . addslashes($value) . "'";
        }
    }
    
    $sql = sprintf(
        "INSERT INTO `%s` (`%s`) VALUES (%s);\n",
        $table_name,
        implode('`, `', $fields),
        implode(', ', $values)
    );
    
    return $sql;
}

/**
 * Форматирует размер файла
 *
 * @param int $bytes Размер в байтах
 * @return string Отформатированный размер
 */
function format_filesize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}


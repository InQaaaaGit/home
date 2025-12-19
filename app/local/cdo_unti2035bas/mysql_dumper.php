<?php

require_once(__DIR__ . '/../../config.php');

defined('MOODLE_INTERNAL') || die();

/**
 * Генерирует SQL для создания таблицы на основе данных
 *
 * @param string $table_name Название таблицы
 * @param array $columns Список колонок
 * @param array $records Записи таблицы для анализа типов
 * @return string SQL для CREATE TABLE
 */
function generate_create_table_sql($table_name, $columns, $records) {
    $sql = "CREATE TABLE `{$table_name}` (\n";

    $column_definitions = [];

    foreach ($columns as $column) {
        // Анализируем данные для определения типа
        $column_type = 'TEXT';
        $is_nullable = false;

        foreach ($records as $record) {
            $value = $record->{$column};

            if (is_null($value)) {
                $is_nullable = true;
                continue;
            }

            // Определяем тип данных
            if ($column === 'id') {
                $column_type = 'INT(10) NOT NULL AUTO_INCREMENT';
                break;
            } elseif (is_int($value) || (is_string($value) && is_numeric($value) && strpos($value, '.') === false)) {
                if ($column_type === 'TEXT') {
                    $column_type = 'INT(10)';
                }
            } elseif (is_float($value) || (is_string($value) && is_numeric($value) && strpos($value, '.') !== false)) {
                $column_type = 'DECIMAL(10,5)';
            } elseif (strlen($value) <= 255) {
                if ($column_type === 'TEXT' || $column_type === 'INT(10)') {
                    $column_type = 'VARCHAR(255)';
                }
            }
        }

        // Добавляем NULL/NOT NULL
        if ($column !== 'id' && $is_nullable) {
            if (strpos($column_type, 'NOT NULL') === false) {
                $column_type .= ' DEFAULT NULL';
            }
        } elseif ($column !== 'id' && strpos($column_type, 'NOT NULL') === false) {
            $column_type .= ' NOT NULL';
        }

        $column_definitions[] = "  `{$column}` {$column_type}";
    }

    $sql .= implode(",\n", $column_definitions);

    // Добавляем PRIMARY KEY если есть id
    if (in_array('id', $columns)) {
        $sql .= ",\n  PRIMARY KEY (`id`)";
    }

    $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";

    return $sql;
}

require_login();
require_capability('moodle/site:config', context_system::instance());

// Получаем параметр LRID (необязательный, для совместимости)
$lrid = optional_param('lrid', '', PARAM_ALPHANUMEXT);

// Список таблиц плагина для дампа
$plugin_tables = [
    'cdo_unti2035bas_stream',
    'cdo_unti2035bas_block',
    'cdo_unti2035bas_module',
    'cdo_unti2035bas_theme',
    'cdo_unti2035bas_activity',
    'cdo_unti2035bas_assessment',
    'cdo_unti2035bas_log',
    'cdo_unti2035bas_xapi_sent'
];

// Обработка скачивания ДОЛЖНА быть ПЕРЕД любым HTML выводом!
$download = optional_param('download', 0, PARAM_INT);
$format = optional_param('format', '', PARAM_ALPHA);

if ($download && $format === 'json') {
    $dump_data = [];

    foreach ($plugin_tables as $table) {
        try {
            $records = $DB->get_records($table);
            $dump_data[$table] = array_values($records);
        } catch (Exception $e) {
            $dump_data[$table] = ['error' => $e->getMessage()];
        }
    }

    // Добавляем метаинформацию
    $dump_data['_metadata'] = [
        'timestamp' => time(),
        'datetime' => date('Y-m-d H:i:s'),
        'moodle_version' => $CFG->version,
        'plugin_version' => get_config('local_cdo_unti2035bas', 'version'),
        'exported_by' => $USER->id,
        'dump_type' => 'full_tables_dump'
    ];

    // Отправляем файл на скачивание
    $filename = "cdo_unti2035bas_full_dump_" . date('Y-m-d_H-i-s') . '.json';
    $json_data = json_encode($dump_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($json_data));
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    echo $json_data;
    die(); // Важно использовать die() вместо exit
}

// Обработка скачивания в SQL формате
if ($download && $format === 'sql') {
    $sql_dump = "";

    // Заголовок SQL дампа
    $sql_dump .= "-- ============================================\n";
    $sql_dump .= "-- CDO UNTI2035 БАС Plugin Database Dump\n";
    $sql_dump .= "-- Сгенерирован: " . date('Y-m-d H:i:s') . "\n";
    $sql_dump .= "-- Moodle версия: " . $CFG->version . "\n";
    $sql_dump .= "-- Пользователь: " . $USER->id . "\n";
    $sql_dump .= "-- ============================================\n\n";

    $sql_dump .= "SET FOREIGN_KEY_CHECKS=0;\n";
    $sql_dump .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $sql_dump .= "SET AUTOCOMMIT = 0;\n";
    $sql_dump .= "START TRANSACTION;\n";
    $sql_dump .= "SET time_zone = \"+00:00\";\n\n";

    foreach ($plugin_tables as $table) {
        try {
            $records = $DB->get_records($table);

            $sql_dump .= "-- ============================================\n";
            $sql_dump .= "-- Дамп данных таблицы `{$table}`\n";
            $sql_dump .= "-- ============================================\n\n";

            if (empty($records)) {
                $sql_dump .= "-- Таблица `{$table}` пуста\n\n";
                continue;
            }

            // Получаем структуру таблицы из первой записи
            $first_record = reset($records);
            $columns = array_keys((array)$first_record);

            // DROP и CREATE TABLE
            $sql_dump .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql_dump .= generate_create_table_sql($table, $columns, $records) . "\n";

            // INSERT данных
            $sql_dump .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES\n";

            $insert_values = [];
            foreach ($records as $record) {
                $values = [];
                foreach ($columns as $column) {
                    $value = $record->{$column};
                    if (is_null($value)) {
                        $values[] = 'NULL';
                    } elseif (is_numeric($value)) {
                        $values[] = $value;
                    } else {
                        $values[] = "'" . addslashes($value) . "'";
                    }
                }
                $insert_values[] = '(' . implode(', ', $values) . ')';
            }

            $sql_dump .= implode(",\n", $insert_values) . ";\n\n";

        } catch (Exception $e) {
            $sql_dump .= "-- ОШИБКА при обработке таблицы {$table}: " . $e->getMessage() . "\n\n";
        }
    }

    $sql_dump .= "SET FOREIGN_KEY_CHECKS=1;\n";
    $sql_dump .= "COMMIT;\n";

    // Отправляем файл на скачивание
    $filename = "cdo_unti2035bas_mysql_dump_" . date('Y-m-d_H-i-s') . '.sql';

    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($sql_dump));
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    echo $sql_dump;
    die(); // Важно использовать die() вместо exit
}

// Настройка страницы
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/cdo_unti2035bas/send_practice.php');
$PAGE->set_title(get_string('pluginname', 'local_cdo_unti2035bas'));
$PAGE->set_heading(get_string('pluginname', 'local_cdo_unti2035bas'));

echo $OUTPUT->header();

echo html_writer::tag('h2', "Полный дамп всех таблиц плагина");

if (!empty($lrid)) {
    echo $OUTPUT->notification("Параметр LRID: {$lrid} (применяется только для совместимости)", 'info');
}

echo html_writer::div(
    html_writer::tag('p', 'Доступные форматы для скачивания:') .
    html_writer::tag('ul',
        html_writer::tag('li', '<strong>JSON</strong> - структурированный формат для программной обработки данных') .
        html_writer::tag('li', '<strong>SQL (MySQL/MariaDB)</strong> - готовый к импорту дамп с CREATE TABLE и INSERT statements')
    ),
    'alert alert-info'
);

// Получаем информацию о таблицах для отображения статистики
$table_stats = [];
foreach ($plugin_tables as $table) {
    try {
        $count = $DB->count_records($table);
        $table_stats[$table] = $count;
    } catch (Exception $e) {
        $table_stats[$table] = 'Ошибка: ' . $e->getMessage();
    }
}

// Отображаем статистику таблиц
echo html_writer::start_tag('div', ['class' => 'table-statistics']);
echo html_writer::tag('h3', 'Статистика таблиц плагина');

$stats_table = new html_table();
$stats_table->attributes['class'] = 'table table-striped table-sm';
$stats_table->head = ['Таблица', 'Количество записей'];
$stats_table->data = [];

foreach ($table_stats as $table => $count) {
    $stats_table->data[] = [
        $table,
        is_numeric($count) ? number_format($count) : $count
    ];
}

echo html_writer::table($stats_table);
echo html_writer::end_tag('div');

// Кнопки для скачивания дампа в разных форматах
$download_json_url = new moodle_url('/local/cdo_unti2035bas/send_practice.php', [
    'format' => 'json',
    'download' => 1
]);

$download_sql_url = new moodle_url('/local/cdo_unti2035bas/send_practice.php', [
    'format' => 'sql',
    'download' => 1
]);

echo html_writer::start_div('mt-3');
echo html_writer::link(
    $download_json_url,
    '📥 Скачать дамп в JSON формате',
    ['class' => 'btn btn-primary me-2']
);

echo html_writer::link(
    $download_sql_url,
    '📥 Скачать дамп в SQL формате (MySQL/MariaDB)',
    ['class' => 'btn btn-success']
);
echo html_writer::end_div();

echo $OUTPUT->footer();

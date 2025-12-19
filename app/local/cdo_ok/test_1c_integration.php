<?php

/**
 * Тестовый скрипт для проверки интеграции с 1С
 * 
 * Использование: запустите из корня Moodle
 * php local/cdo_ok/test_1c_integration.php
 */

define('CLI_SCRIPT', true);

// Для Docker окружения путь к config.php может быть другим
$config_paths = [
    __DIR__ . '/../../config.php',
    __DIR__ . '/../../../config.php',
    '/home/inq/projects/moodle4container/config.php'
];

foreach ($config_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        break;
    }
}

if (!isset($CFG)) {
    die("Не удалось найти config.php Moodle\n");
}

require_once($CFG->libdir . '/clilib.php');

use local_cdo_ok\services\service_ok_1c;
use local_cdo_ok\reports\report_trait;

cli_heading('Тест интеграции с 1С для CDO OK');

// Получаем ID пользователей из ответов
echo "Шаг 1: Получаем ID пользователей из ответов...\n";

$user_ids_raw = $DB->get_records_sql(
    'SELECT DISTINCT user_id FROM {local_cdo_ok_answer}'
);

$user_ids = [];
foreach ($user_ids_raw as $record) {
    $user_ids[] = $record->user_id;
}

echo sprintf("Найдено %d уникальных пользователей\n", count($user_ids));
echo "User IDs: " . implode(', ', array_slice($user_ids, 0, 10)) . 
     (count($user_ids) > 10 ? '...' : '') . "\n\n";

// Проверяем настройки плагина tool_cdo_config
echo "Шаг 2: Проверяем настройки интеграции...\n";

try {
    $config_plugin = get_config('tool_cdo_config');
    if (empty($config_plugin)) {
        echo "❌ Плагин tool_cdo_config не настроен!\n";
        echo "   Настройте интеграцию в: Site administration → Plugins → Tool CDO Config\n\n";
    } else {
        echo "✅ Плагин tool_cdo_config найден\n\n";
    }
} catch (Exception $e) {
    echo "❌ Ошибка при проверке конфигурации: " . $e->getMessage() . "\n\n";
}

// Пытаемся получить данные из 1С
echo "Шаг 3: Запрашиваем данные из 1С...\n";

try {
    $service = new service_ok_1c();
    $result = $service->get_users_information(json_encode(array_slice($user_ids, 0, 5)));
    
    echo sprintf("✅ Получено записей: %d\n", count($result));
    
    if (count($result) > 0) {
        echo "\nПример первой записи:\n";
        $first = reset($result);
        echo "  user_id: " . ($first->user_id ?? 'НЕТ') . "\n";
        echo "  fio: " . ($first->fio ?? 'НЕТ') . "\n";
        echo "  edu_spec: " . ($first->edu_spec ?? 'НЕТ') . "\n";
        echo "  edu_structure: " . ($first->edu_structure ?? 'НЕТ') . "\n";
        echo "  group: " . ($first->group ?? 'НЕТ') . "\n";
    } else {
        echo "⚠️  Данные не получены из 1С\n";
        echo "   Возможные причины:\n";
        echo "   - Сервис 1С не отвечает\n";
        echo "   - Нет данных для указанных пользователей\n";
        echo "   - Неправильная настройка интеграции\n";
    }
    
} catch (Throwable $e) {
    echo "❌ Ошибка при запросе к 1С:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "\n   Trace:\n";
    echo "   " . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "Тест завершен\n";


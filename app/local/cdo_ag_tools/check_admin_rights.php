<?php

require_once(__DIR__ . "/../../config.php");
require_login();

// Проверяем, является ли пользователь администратором
if (!is_siteadmin()) {
    echo "<h1 style='color: red;'>ОШИБКА: Пользователь не является администратором</h1>";
    echo "<p>Текущий пользователь: {$USER->username} ({$USER->firstname} {$USER->lastname})</p>";
    exit;
}

echo "<h1>Проверка прав администратора: {$USER->username}</h1>";

// Проверяем системные права
$context = context_system::instance();

$capabilities = [
    'webservice/rest:use',
    'moodle/grade:edit',
    'mod/assign:grade',
    'moodle/site:config',
    'moodle/site:configview'
];

echo "<h2>Проверка прав доступа:</h2>";
foreach ($capabilities as $capability) {
    $has_capability = has_capability($capability, $context);
    $status = $has_capability ? '✅ Есть' : '❌ Нет';
    echo "<p><strong>{$capability}:</strong> {$status}</p>";
}

// Проверяем настройки веб-сервисов
echo "<h2>Проверка настроек веб-сервисов:</h2>";

// Проверяем, включен ли REST протокол
$rest_enabled = get_config('webservice', 'enableprotocols_rest');
echo "<p><strong>REST протокол включен:</strong> " . ($rest_enabled ? '✅ Да' : '❌ Нет') . "</p>";

// Проверяем, включены ли веб-сервисы вообще
$webservices_enabled = get_config('core', 'enablewebservices');
echo "<p><strong>Веб-сервисы включены:</strong> " . ($webservices_enabled ? '✅ Да' : '❌ Нет') . "</p>";

// Проверяем существование нашего сервиса
$services = $DB->get_records('external_services', ['component' => 'local_cdo_ag_tools']);
echo "<p><strong>Наш сервис зарегистрирован:</strong> " . (count($services) > 0 ? '✅ Да' : '❌ Нет') . "</p>";

if (count($services) > 0) {
    foreach ($services as $service) {
        echo "<p>Сервис: {$service->name} (ID: {$service->id}, Включен: " . ($service->enabled ? 'Да' : 'Нет') . ")</p>";
    }
}

// Проверяем, есть ли у администратора токен
$tokens = $DB->get_records('external_tokens', ['userid' => $USER->id]);
echo "<p><strong>Токены пользователя:</strong> " . count($tokens) . "</p>";

if (count($tokens) > 0) {
    foreach ($tokens as $token) {
        $service = $DB->get_record('external_services', ['id' => $token->externalserviceid]);
        echo "<p>Токен: {$token->token} (Сервис: " . ($service ? $service->name : 'Unknown') . ")</p>";
    }
}

// Тестируем прямую работу функции
echo "<h2>Тестирование функции set_category_and_course_grades:</h2>";

try {
    // Получаем первый курс для теста
    $courses = $DB->get_records('course', [], 'id ASC', 'id,fullname', 0, 1);
    if (!$courses) {
        echo "<p style='color: orange;'>Нет доступных курсов для теста</p>";
    } else {
        $course = reset($courses);
        echo "<p>Тестируем на курсе: {$course->fullname} (ID: {$course->id})</p>";
        
        // Получаем первую категорию оценок для теста
        $categories = $DB->get_records('grade_categories', ['courseid' => $course->id], 'id ASC', 'id,fullname', 0, 1);
        if (!$categories) {
            echo "<p style='color: orange;'>Нет категорий оценок в курсе</p>";
        } else {
            $category = reset($categories);
            echo "<p>Категория оценок: {$category->fullname} (ID: {$category->id})</p>";
            
            // Тестируем функцию
            $result = \local_cdo_ag_tools\external\grades::set_category_and_course_grades(
                $course->id,
                'category',
                $category->fullname,
                $USER->id,
                85.5
            );
            
            echo "<pre>" . print_r($result, true) . "</pre>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Ошибка при тестировании функции: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>Рекомендации:</h2>";
echo "<ol>";
echo "<li>Убедитесь, что веб-сервисы включены в настройках Moodle</li>";
echo "<li>Убедитесь, что REST протокол включен</li>";
echo "<li>Проверьте, что сервис 'cdo_ag_tools_API' создан и включен</li>";
echo "<li>Создайте токен для администратора</li>";
echo "</ol>";

?>

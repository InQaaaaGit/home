<?php

require_once(__DIR__ . "/../../../config.php");
require_login();

// Проверяем, является ли пользователь администратором
if (!is_siteadmin()) {
    echo "<h1 style='color: red;'>ОШИБКА: Только администратор может выполнять эту настройку</h1>";
    exit;
}

echo "<h1>Автоматическая настройка веб-сервисов для плагина tool_cdo_showcase_tools</h1>";

// Включаем веб-сервисы
set_config('enablewebservices', 1);

// Включаем REST протокол
$protocols = get_config('webservice', 'enableprotocols');
if ($protocols) {
    $protocols_arr = explode(',', $protocols);
    if (!in_array('rest', $protocols_arr)) {
        $protocols_arr[] = 'rest';
        set_config('enableprotocols', implode(',', $protocols_arr), 'webservice');
    }
} else {
    set_config('enableprotocols', 'rest', 'webservice');
}

echo "<p>✅ Веб-сервисы включены</p>";
echo "<p>✅ REST протокол включен</p>";

// Проверяем существование нашего сервиса
$existing_service = $DB->get_record('external_services', [
    'component' => 'tool_cdo_showcase_tools',
    'shortname' => 'cdo_showcase_tools_service'
]);

if ($existing_service) {
    echo "<p>✅ Сервис 'cdo_showcase_tools_service' уже существует (ID: {$existing_service->id})</p>";
    
    // Обновляем сервис
    $existing_service->enabled = 1;
    $existing_service->restrictedusers = 0;
    $existing_service->downloadfiles = 0;
    $existing_service->uploadfiles = 0;
    $DB->update_record('external_services', $existing_service);
    echo "<p>✅ Сервис обновлен</p>";
} else {
    // Создаем новый сервис
    $service = new stdClass();
    $service->name = 'CDO Showcase Tools Service';
    $service->enabled = 1;
    $service->requiredcapability = '';
    $service->restrictedusers = 0;
    $service->component = 'tool_cdo_showcase_tools';
    $service->timecreated = time();
    $service->timemodified = time();
    $service->shortname = 'cdo_showcase_tools_service';
    $service->downloadfiles = 0;
    $service->uploadfiles = 0;
    
    $service_id = $DB->insert_record('external_services', $service);
    echo "<p>✅ Сервис 'cdo_showcase_tools_service' создан (ID: {$service_id})</p>";
}

// Добавляем функции в сервис
$service_id = $existing_service ? $existing_service->id : $service_id;

// Удаляем старые функции сервиса
$DB->delete_records('external_services_functions', ['externalserviceid' => $service_id]);

// Получаем список функций из нашего сервиса
$functions = [
    'core_webservice_get_site_info',
    'core_course_get_courses',
    'tool_cdo_showcase_tools_get_course_letter_grades',
    'tool_cdo_showcase_tools_get_users_courses',
    'tool_cdo_showcase_tools_get_courses',
    'tool_cdo_showcase_tools_get_grade_items',
    'tool_cdo_showcase_tools_get_courses_by_category',
];

// Добавляем все функции в сервис
foreach ($functions as $function_name) {
    $function = new stdClass();
    $function->externalserviceid = $service_id;
    $function->functionname = $function_name;
    $DB->insert_record('external_services_functions', $function);
    echo "<p>✅ Функция '{$function_name}' добавлена в сервис</p>";
}

// Создаем токен для текущего администратора
$existing_token = $DB->get_record('external_tokens', [
    'userid' => $USER->id,
    'externalserviceid' => $service_id
]);

if ($existing_token) {
    echo "<p>✅ Токен уже существует: {$existing_token->token}</p>";
} else {
    $token = new stdClass();
    $token->token = md5(uniqid(rand(), true));
    $token->privatetoken = null;
    $token->tokentype = 0;
    $token->userid = $USER->id;
    $token->externalserviceid = $service_id;
    $token->contextid = 1;
    $token->creatorid = $USER->id;
    $token->iprestriction = null;
    $token->validuntil = null;
    $token->timecreated = time();
    $token->lastaccess = null;
    $token->name = 'Admin Token for tool_cdo_showcase_tools';
    
    $token_id = $DB->insert_record('external_tokens', $token);
    echo "<p>✅ Новый токен создан: {$token->token}</p>";
}

// Очищаем кэш настроек
purge_all_caches();

echo "<h2>Настройка завершена!</h2>";
echo "<p><strong>Токен для использования API:</strong> " . ($existing_token ? $existing_token->token : $token->token) . "</p>";

echo "<h3>Пример использования метода tool_cdo_showcase_tools_get_courses_by_category:</h3>";
echo "<pre>";
echo "URL: " . $CFG->wwwroot . "/webservice/rest/server.php\n";
echo "Метод: POST\n";
echo "Параметры:\n";
echo "wstoken: " . ($existing_token ? $existing_token->token : $token->token) . "\n";
echo "wsfunction: tool_cdo_showcase_tools_get_courses_by_category\n";
echo "moodlewsrestformat: json\n";
echo "options[ids]: [] (опционально)\n";
echo "categoryId: [ID категории]\n";
echo "</pre>";

echo "<h3>Доступные функции:</h3>";
echo "<ul>";
foreach ($functions as $function_name) {
    echo "<li>{$function_name}</li>";
}
echo "</ul>";

?>



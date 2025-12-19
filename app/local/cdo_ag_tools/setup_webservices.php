<?php

require_once(__DIR__ . "/../../config.php");
require_login();

// Проверяем, является ли пользователь администратором
if (!is_siteadmin()) {
    echo "<h1 style='color: red;'>ОШИБКА: Только администратор может выполнять эту настройку</h1>";
    exit;
}

echo "<h1>Автоматическая настройка веб-сервисов для плагина cdo_ag_tools</h1>";

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
    'component' => 'local_cdo_ag_tools',
    'name' => 'cdo_ag_tools_API'
]);

if ($existing_service) {
    echo "<p>✅ Сервис 'cdo_ag_tools_API' уже существует (ID: {$existing_service->id})</p>";
    
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
    $service->name = 'cdo_ag_tools_API';
    $service->enabled = 1;
    $service->requiredcapability = '';
    $service->restrictedusers = 0;
    $service->component = 'local_cdo_ag_tools';
    $service->timecreated = time();
    $service->timemodified = time();
    $service->shortname = 'cdo_ag_tools_API';
    $service->downloadfiles = 0;
    $service->uploadfiles = 0;
    
    $service_id = $DB->insert_record('external_services', $service);
    echo "<p>✅ Сервис 'cdo_ag_tools_API' создан (ID: {$service_id})</p>";
}

// Добавляем функции в сервис
$service_id = $existing_service ? $existing_service->id : $service_id;

// Удаляем старые функции сервиса
$DB->delete_records('external_services_functions', ['externalserviceid' => $service_id]);

// Добавляем нашу функцию
$function = new stdClass();
$function->externalserviceid = $service_id;
$function->functionname = 'local_cdo_ag_tools_set_category_and_course_grades';
$DB->insert_record('external_services_functions', $function);

echo "<p>✅ Функция 'local_cdo_ag_tools_set_category_and_course_grades' добавлена в сервис</p>";

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
    $token->name = 'Admin Token for cdo_ag_tools';
    
    $token_id = $DB->insert_record('external_tokens', $token);
    echo "<p>✅ Новый токен создан: {$token->token}</p>";
}

// Очищаем кэш настроек
purge_all_caches();

echo "<h2>Настройка завершена!</h2>";
echo "<p><strong>Токен для использования API:</strong> " . ($existing_token ? $existing_token->token : $token->token) . "</p>";

echo "<h3>Пример использования:</h3>";
echo "<pre>";
echo "URL: " . $CFG->wwwroot . "/webservice/rest/server.php\n";
echo "Метод: POST\n";
echo "Параметры:\n";
echo "wstoken: " . ($existing_token ? $existing_token->token : $token->token) . "\n";
echo "wsfunction: local_cdo_ag_tools_set_category_and_course_grades\n";
echo "moodlewsrestformat: json\n";
echo "courseid: [ID курса]\n";
echo "gradetype: category|course\n";
echo "categoryname: [Название категории]\n";
echo "userid: [ID пользователя]\n";
echo "grade: [Оценка]\n";
echo "</pre>";

echo "<p><a href='check_admin_rights.php'>Проверить настройки</a></p>";

?>

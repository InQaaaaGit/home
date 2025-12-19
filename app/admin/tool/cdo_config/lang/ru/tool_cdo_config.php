<?php

//errors

$string['info_not_found'] = 'Информация не найдена';
$string['nosectionmodules'] = 'Модули не найдены в секции {$a}';
$string['noassignmentfound'] = 'Задание не найдено в секции {$a}';
$string['gradeitemnotfound'] = 'Элемент оценки не найден для задания';

//plugin info

$string['plugin_short_name'] = 'ЭИОС';
$string['pluginname'] = 'ЭИОС';
$string['plugin_full_name'] = 'Электронная информационная образовательная среда';
$string['tool_cdo_config_integrations'] = 'Интеграции';
$string['name_cdo_config_integrations'] = 'Список запросов';

$string['root_category'] = 'ЭИОС';

//plugin info

//setting_integration_form

//main group
$string['setting_integration_form_main'] = 'Основная информация';

$string['setting_integration_form_name'] = 'Название запроса';
$string['setting_integration_form_name_help'] = 'Название запроса help';

$string['setting_integration_form_description'] = 'Описание запроса';
$string['setting_integration_form_description_help'] = 'Описание запроса help';

$string['setting_integration_form_method'] = 'Используемый метод запроса';
$string['setting_integration_form_method_help'] = 'Используемый метод запроса help';

$string['setting_integration_form_endpoint'] = 'Адрес запроса';
$string['setting_integration_form_endpoint_help'] = 'Адрес запроса help';

$string['setting_integration_form_port'] = 'Порт запроса';
$string['setting_integration_form_port_help'] = 'Порт запроса help';
//main group
//auth group
$string['setting_integration_form_auth'] = 'Авторизация';

$string['setting_integration_form_no_auth'] = 'Не используется авторизация';
$string['setting_integration_form_no_auth_help'] = 'Не используется авторизация help';

$string['setting_integration_form_auth_token'] = 'Авторизация по токену';
$string['setting_integration_form_auth_token_help'] = 'Авторизация по токену help';

$string['setting_integration_form_login'] = 'Логин';
$string['setting_integration_form_login_help'] = 'Логин help';

$string['setting_integration_form_password'] = 'Пароль';
$string['setting_integration_form_password_help'] = 'Пароль help';

$string['setting_integration_form_type_token'] = 'Тип токена';
$string['setting_integration_form_type_token_help'] = 'Тип токена help';

$string['setting_integration_form_token'] = 'Токена';
$string['setting_integration_form_token_help'] = 'Токена help';
//auth group
//call group
$string['setting_integration_form_call'] = 'Обработка';

$string['setting_integration_form_code'] = 'Код вызова интеграции';
$string['setting_integration_form_code_help'] = 'Код вызова интеграции help';

$string['setting_integration_form_dto'] = 'Путь к классу DTO';
$string['setting_integration_form_dto_help'] = 'Путь к классу DTO help';
//call group
//other group
$string['setting_integration_form_other'] = 'Дополнительно';

$string['setting_integration_form_headers'] = 'Заголовки';
$string['setting_integration_form_headers_help'] = 'Заголовки help';

$string['setting_integration_form_use_mock'] = 'Использовать тестовые данные';
$string['setting_integration_form_use_mock_help'] = 'Использовать тестовые данные help';

$string['setting_integration_form_mock'] = 'Тестовые данные';
$string['setting_integration_form_mock_help'] = 'Тестовые данные help';
//other group

//error messages
$string['setting_integration_form_required_param'] = "Обязательный параметр";
$string['setting_integration_form_is_use_param'] = "Данный код уже используется другой функцией";
$string['setting_integration_form_empty_class'] = "Не удалось найти указанный класс";
//error messages

//setting_integration_form

//pages

//settings/integrations/list
$string['settings_integrations_list_list_request'] = "Список запросов";
//settings/integrations/list
//settings/integrations/single
$string['settings_integrations_single_deleted'] = "Запись успешно удалена!";
$string['settings_integrations_single_saved'] = "Запись успешно сохранена!";
$string['settings_integrations_single_create_request'] = "Создание запроса";
$string['settings_integrations_single_edit_request'] = 'Редактирование {$a}';
//settings/integrations/single

//pages

//js

$string['js_modal_title_deleted'] = 'Удаление';
$string['js_modal_question_deleted'] = "Вы уверены, что хотите удалить выбранный элемент?";
$string['js_modal_yes_label_deleted'] = "Продолжить";
$string['js_modal_no_label_deleted'] = "Отмена";

//js

$string['col_name'] = 'Название';
$string['col_method'] = 'Метод';
$string['col_code'] = 'Код запроса';
$string['col_actions'] = 'Действия';

$string['no_records'] = 'Нет записей';
$string['act_create'] = 'Создать';
$string['act_update'] = 'Обновить';
$string['act_delete'] = 'Удалить';

$string['wrong_code_in_exc'] = 'Код ошибки {$a} не обнаружен';
$string['exc_gradebook_notfound'] = 'Зачетная книга не найдена';
$string['exc_token_notfound'] = 'Не удалось найти токен';
$string['exc_login_notfound'] = 'Не удалось найти логин';
$string['exc_password_notfound'] = 'Не удалось найти пароль';
$string['exc_empty_token'] = 'Токен пустой';

$string['exc_record_not_found_by_id'] = 'Не удалось найти запись по указанному идентификатору!';
$string['exc_request_not_found_by_code'] = 'Не удалось найти запрос по коду!';
$string['exc_byte_formation'] = 'Ошибка формирования байт';
$string['exc_wrong_string_for_decode'] = 'Передана некорректная строка для расшифровки';
$string['exc_service_not_found'] = 'Не удалось найти запрашиваемый сервис для перехода!';
$string['exc_not_filled_required_fields'] = 'Не заполнены обязательные параметры для получения ссылки для перехода';
$string['exc_close_statement'] = 'Ошибка при закрытие ведомости';
$string['exc_db_record_not_found'] = 'Запись в БД не найдена';
$string['exc_resource_not_found'] = 'Не удалось найти ресурс';
$string['exc_access_denied'] = 'Отказано в доступе';
$string['exc_timed_out'] = 'Истекло время ожидания';

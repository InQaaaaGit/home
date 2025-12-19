<?php

//main
$string['pluginname'] = "Аттестационные ведомости";
$string['short_name'] = "Ведомости";
$string['access_denied'] = 'У Вас нет доступа к разделу {$a}!<br>Обратитесь к администратору!';
$string['settings_page'] = "Аттестационные ведомости. Основные настройки";
//main
//form
$string['setting_form_main'] = "Основная информация";
$string['setting_form_code'] = "Код ведомости с комиссией";
$string['required_param'] = "Обязательно к заполнению!";
$string['setting_form_code_help'] = "<p>
По данному коду будет определяться является ли данная ведомость, ведомостью с комиссией. Данный код берется из поля type_code
</p>";
//form

//tabs
$string['tabs_agreement'] = "Подписать";
//tabs

//agreed js
$string['grades_confirm_change_agreed_title'] = "Подтверждение";
$string['grades_confirm_change_agreed_message'] = "Уверены что хотите согласовать ведомость?";
$string['grades_confirm_change_agreed_yes'] = "Подтвердить";
$string['grades_confirm_change_agreed_no'] = "Отмена";
$string['grades_alert_change_agreed_title'] = "Согласование ведомости";
$string['grades_alert_change_agreed_message'] = "Согласование успешно сохранено";
$string['grades_alert_change_agreed_yes'] = "Закрыть";
//agreed js

//close js
$string['grades_confirm_change_close_title'] = "Подтверждение";
$string['grades_confirm_change_close_message'] = "Уверены что хотите закрыть ведомость?";
$string['grades_confirm_change_close_yes'] = "Подтвердить";
$string['grades_confirm_change_close_no'] = "Отмена";
$string['grades_alert_change_close_title'] = "Закрытие ведомости";
$string['grades_alert_change_close_message'] = "Ведомость успешно закрыта!";
$string['grades_alert_change_close_yes'] = "Закрыть";
//close js

//grades js
$string['grades_confirm_change_grade_title'] = "Подтверждение";
$string['grades_confirm_change_grade_message'] = "Уверены что хотите изменить оценку для пользователя?";
$string['grades_confirm_change_grade_yes'] = "Подтвердить";
$string['grades_confirm_change_grade_no'] = "Отмена";
$string['grades_alert_change_grade_title'] = "Выставление оценки";
$string['grades_alert_change_grade_message'] = "Оценка успешно изменена";
$string['grades_alert_change_grade_yes'] = "Закрыть";
//grades js

//sheet api
$string['sheet_api_guid_grade'] = "GUID оценки";
$string['sheet_api_guid_save_grade'] = "GUID сохраненной оценки";
$string['sheet_api_guid_student'] = "GUID студента";
$string['sheet_api_guid_sheet'] = "GUID ведомости";
$string['sheet_api_change_current_grade'] = "Параметры для изменения текущей оценки";
$string['sheet_api_structure_info_about_grade'] = "Структура информации об оценке";
$string['sheet_api_list_current_grades'] = "Список текущих оценках";
$string['sheet_api_user_id'] = "ID пользователя";
$string['sheet_api_agreed_sheet'] = "Параметры для согласования ведомости";
$string['sheet_api_sheet_is_empty'] = "Не удалось найти ведомость";
$string['sheet_api_count_student_change'] = "Количество студентов в изначальной и сохраненной ведомости отличается";
$string['sheet_api_student_not_found'] = "Не удалось найти студента";
$string['sheet_api_grades_not_match'] = "Оценки не совпадают";
$string['sheet_api_structure_close_sheet'] = "Параметры для закрытия ведомости";
//sheet api

//close sheet
$string['close_sheet_close_button'] = "Закрыть ведомость";
$string['close_sheet_reload_button'] = "Перезагрузить ведомость";
//close sheet
//commission sheet
$string['commission_sheet_agreed_message_yes'] = "Подписано";
$string['commission_sheet_agreed_message_no'] = "Не подписано";
$string['commission_sheet_user_full_name'] = "ФИО";
$string['commission_sheet_activity'] = "Подтверждение";
$string['commission_sheet_agreed'] = "Согласовать";
$string['commission_sheet_chairman'] = "Председатель";
//commission sheet
//list sheet
$string['list_sheet_not_found_open_sheet'] = "Не удалось найти открытые ведомости";
//list sheet
//grade element
$string['grade_element_not_grades'] = "Не оценено";
$string['point_control_event'] = "Баллов за контрольное мероприятие";
$string['point_semester'] = "Баллов за семестр";
$string['absence'] = "Неявка";

//grade element
//table sheet
$string['table_sheet_user_full_name'] = "ФИО студента";
$string['table_sheet_grade_book'] = "Зачетная книга";
$string['table_sheet_grade'] = "Отметка";
$string['table_sheet_teacher_grade'] = "Поставил отметку";
//table sheet
//info sheet
$string['sheet_name_plan'] = "Учебный план";
$string['sheet_group'] = "Группа";
$string['sheet_profile'] = "Профиль";
$string['sheet_semester'] = "Семестр";
$string['sheet_division'] = "Подразделение";
$string['sheet_form_education'] = "Форма обучения";
$string['sheet_level_education'] = "Уровень образования";
$string['sheet_specialty'] = "Специальность";
$string['sheet_course'] = "Курс";
$string['sheet_guid'] = "Уникальный идентификатор";
$string['sheet_discipline'] = "Предмет";
$string['sheet_type_control'] = "Вид контроля";
$string['sheet_name_sheet'] = "Название ведомости";
$string['sheet_type'] = "Тип ведомости";
$string['sheet_type_code'] = "Код типа ведомости";
$string['sheet_points_semester'] = "Балл за семестр";
$string['sheet_control_event'] = "Балл за контрольное мероприятие";
$string['sheet_theme_placeholder'] = "Впишите название темы курсовой";
$string['sheet_theme'] = "Тема курсовой работы";
$string['sheet_download'] = "Скачать ведомость";
$string['sheet_date'] = "Дата ведомости";

//info sheet

//toast
$string['toast_success'] = "Успешно!";

//errors
$string['sheet_guid_not_found'] = "В структуре ведомости отсутствует guid или он некорректный!";

//settings
$string['show_BRS'] = "Участвующие уровни подготовки в БРС";
$string['division_for_BRS'] = "Участвующие Высшие школы в БРС";
$string['show_BRS_description'] = "Укажите через запятую (,) наименования из 1С уровней подготовки";
$string['division_for_BRS_description'] = "Укажите через запятую (,) наименования из 1С школ";
$string['guid_absence'] = "GUID отметки \"Неявка\" из 1С";
$string['guid_absence_description'] = "Получите 36-значный GUID из 1с";
$string['guid_absence_not_set'] = "GUID отметки Неявка не установлен. Обратитесь к администрации";

//grades
$string['grade_unsatisfactory'] = 'Не удовлетворительно';
$string['grade_satisfactory'] = 'Удовлетворительно';
$string['grade_good'] = 'Хорошо';
$string['grade_excellent'] = 'Отлично';
$string['average_discipline_rating'] = 'Средний рейтинг по дисциплине (СРД)';
$string['rating_intermediate_certification_discipline'] = 'Рейтинг промежуточной аттестации по дисциплине (РПАД)';
$string['final_rating_discipline'] = 'Итоговый рейтинг по дисциплине (ИРД)';
$string['ysc_competence_level'] = 'Уровень сформированности компетенций в рамках дисциплины';
$string['commission_sheet_title'] = 'Комиссия';
$string['current_grade'] = 'Текущая отметка';
$string['absence_grade'] = 'Неявка';
$string['sheet_tab_name'] = 'Ведомость';
$string['loading'] = 'Загрузка...';

//vue settings
$string['enable_vue_components'] = "Использовать Vue компоненты";
$string['enable_vue_components_description'] = "Включить использование Vue.js компонентов для рендеринга основного приложения";

// layout settings
$string['layout_settings'] = 'Настройки компоновки';
$string['layout_settings_desc'] = 'Здесь вы можете настроить компоновку аттестационных ведомостей.';
$string['show_left_panel'] = 'Показывать левую панель';
$string['show_left_panel_desc'] = 'Показать/скрыть левую панель с основной информацией.';
$string['show_right_panel'] = 'Показывать правую панель';
$string['show_right_panel_desc'] = 'Показать/скрыть правую панель с дополнительной информацией.';
$string['layout_type'] = 'Тип компоновки';
$string['layout_type_desc'] = 'Выберите тип компоновки для аттестационных ведомостей.';
$string['layout_type_default'] = 'По умолчанию';
$string['layout_type_two_rows'] = 'Две строки';
$string['layout_type_vertical'] = 'Вертикальная';
$string['advanced_layout'] = 'Расширенная компоновка';
$string['advanced_layout_desc'] = 'Расширенные настройки компоновки для пользовательских компонентов.';
$string['enable_custom_components'] = 'Включить пользовательские компоненты';
$string['enable_custom_components_desc'] = 'Включить/отключить пользовательские компоненты Vue.js в компоновке.';
$string['show_download_button'] = 'Показывать кнопку скачивания';
$string['show_download_button_description'] = 'Показать/скрыть кнопку скачивания для аттестационной ведомости.';

//error page
$string['error_page_title'] = 'Ошибка';
$string['error_download_failed'] = 'Не удалось скачать файл. Пожалуйста, попробуйте позже или обратитесь к администратору.';
$string['error_connection'] = 'Ошибка подключения. Пожалуйста, проверьте ваше интернет-соединение и попробуйте снова.';
$string['error_invalid_certificate'] = 'Недействительный сертификат. Пожалуйста, проверьте данные и попробуйте снова.';
$string['error_permission_denied'] = 'Доступ запрещен. У вас нет прав для выполнения этого действия.';
$string['error_general'] = 'Произошла ошибка. Пожалуйста, попробуйте позже или обратитесь к администратору.';
$string['error_occurred'] = 'Произошла ошибка';
$string['error_type_download'] = 'Ошибка загрузки';
$string['error_type_connection'] = 'Ошибка подключения';
$string['error_type_invalid'] = 'Недействительные данные';
$string['error_type_permission'] = 'Доступ запрещен';
$string['error_type_general'] = 'Общая ошибка';
$string['error_troubleshooting'] = 'Рекомендации по решению проблемы:';
$string['error_tip_network'] = 'Проверьте ваше интернет-соединение';
$string['error_tip_retry'] = 'Попробуйте повторить операцию через несколько минут';
$string['error_tip_contact'] = 'Если проблема сохраняется, обратитесь к администратору системы';
$string['error_return'] = 'Вернуться назад';
$string['error_retry'] = 'Повторить попытку';
$string['error_debug_info'] = 'Отладочная информация (только для администраторов)';
$string['error_code'] = 'Код ошибки';
$string['error_message'] = 'Сообщение об ошибке';
$string['error_time'] = 'Время ошибки';

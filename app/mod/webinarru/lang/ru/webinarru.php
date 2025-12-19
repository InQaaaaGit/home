<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     mod_webinarru
 * @category    string
 * @copyright   2023 Maks Grishin (CDO Global@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Общие
$string['pluginname'] = 'mts-link.ru by CDO Global';
$string['modulename'] = 'Видеоконференция на mts-link.ru';
$string['modulenameplural'] = 'Видеоконференции на mts-link.ru';

// Форма для создания/обновления экземпляра модуля
$string['mod_form/help_button'] = 'Ознакомиться с инструкцией по данному элементу курса';
$string['mod_form/designed_by'] = 'Разработано by CDO Global';
$string['mod_form/purpose'] = 'Назначение видеоконференции';
$string['mod_form/purpose_desc'] = 'Отображается в названии модуля в курсе и на сервисе mts-link.ru<br><b>Не пренебрегайте</b>';
$string['mod_form/webinar_date'] = 'Дата проведения';
$string['mod_form/webinar_date_desc'] = 'От этого параметра зависит проверка доступности дат создания видеоконференции<br><b>Будьте внимательны</b>';
$string['mod_form/webinar_duration'] = 'Продолжительность';
$string['mod_form/webinar_duration_desc'] = 'От этого параметра зависит проверка доступности дат создания видеоконференции<br><b>Будьте внимательны</b>';
$string['mod_form/check_free_time'] = 'Проверить свободное время на выбранную дату проведения';
$string['mod_form/show_selected_date'] = 'Отобразить выбранный диапазон времени';
$string['mod_form/submitbutton1'] = 'Сохранить и перейти к mts-link.ru';
$string['mod_form/submitbutton2'] = 'Сохранить и вернуться к курсу';

// Массив со значениями назначения видеоконференции
$string['mod_form/purpose/credit'] = 'Зачет';
$string['mod_form/purpose/exam'] = 'Экзамен';

$string['mod_form/type/webinar'] = 'Вебинар';
$string['mod_form/type/meeting'] = 'Встреча';
$string['mod_form/type/training'] = 'Тренинг';
$string['mod_form/type'] = 'Тип';

// Массив со значениями продолжительности видеоконференции
$string['mod_form/webinar_duration/900'] = '15 минут';
$string['mod_form/webinar_duration/1800'] = '30 минут';
$string['mod_form/webinar_duration/2700'] = '45 минут';
$string['mod_form/webinar_duration/3600'] = '1 час';
$string['mod_form/webinar_duration/5400'] = '1 час 30 минут';
$string['mod_form/webinar_duration/7200'] = '2 часа';
$string['mod_form/webinar_duration/9000'] = '2 часа 30 минут';
$string['mod_form/webinar_duration/10800'] = '3 часа';

// Форма настройки модуля
$string['settings/general'] = 'Используемые аккаунты mts-link.ru';
$string['settings/general_desc'] = 'Эти параметры используются <b>всегда</b>';
$string['settings/accounts'] = 'Данные учетных записей';
$string['settings/accounts_desc'] = 'Формат данных - JSON<br>Количество указываемых аккаунтов не ограничено';
$string['settings/example'] = 'Пример данных';
$string['settings/example_desc'] = '<pre>{<br>  "0": {<br>    "login": "test_1@example.ru",<br>    "password": "password_1",<br>    "token": "token_1"<br>  },<br>  "1": {<br>    "login": "test_2@example.ru",<br>    "password": "password_2",<br>    "token": "token_2"<br>  },<br>  "2": {<br>    "login": "test_3@example.ru",<br>    "password": "password_3",<br>    "token": "token_3"<br>  }<br>}<pre>';
$string['settings/api'] = 'Настройки API';
$string['settings/api_desc'] = 'Параметры подключения к API сервиса видеоконференций';
$string['settings/userapi_url'] = 'URL User API';
$string['settings/userapi_url_desc'] = 'Базовый URL для User API (например, https://userapi.mts-link.ru)';
$string['settings/events_url'] = 'URL Events API';
$string['settings/events_url_desc'] = 'Базовый URL для Events API (например, https://events.mts-link.ru)';
$string['settings/functions'] = 'Конфигурация для отдельных функций видеоконференций';
$string['settings/functions_desc'] = 'Эти параметры используются <b>всегда</b>';
$string['settings/free_access'] = 'Свободный доступ';
$string['settings/free_access_desc'] = '<b>Если отмечено</b> - на видеоконференцию может войти любой желающий';
$string['settings/auto_start'] = 'Автозапуск';
$string['settings/auto_start_desc'] = '<b>Если отмечено</b> - видеоконференция начнется автоматически в заданное время';
$string['settings/form'] = 'Изменение формы создания/обновления элемента курса';
$string['settings/form_desc'] = 'Эти параметры влияют на отображение страницы создания/обновления элемента курса';
$string['settings/show_calendar'] = 'Показывать календарь';
$string['settings/show_calendar_desc'] = '<b>Если не отмечено</b> - календарь справа от выбора даты будет скрыт';
$string['settings/show_selected_date'] = 'Показывать выбранный диапазон времени';
$string['settings/show_selected_date_desc'] = '<b>Если не отмечено</b> - по умолчанию диапазон не будет отображаться (пользователь может включить отображение)';
$string['settings/change_submit_buttons'] = 'Изменить названия кнопок сохранения элемента курса';
$string['settings/change_submit_buttons_desc'] = '<b>Если не отмечено</b> - кнопки будут иметь названия по умолчанию';
$string['settings/disable_tags'] = 'Отключить редактирование тегов';
$string['settings/disable_tags_desc'] = '<b>Если не отмечено</b> - поле для редактирования тегов будет доступно';
$string['settings/help'] = 'Дополнительно';
$string['settings/help_desc'] = 'Дополнительные параметры';
$string['settings/show_help'] = 'Отображать уведомление для перехода к инструкции';
$string['settings/show_help_desc'] = '<b>Если отмечено</b> - на странице создания/редактирования элемента курса будет уведомление с кнопкой для перехода к инструкции';
$string['settings/url_help'] = 'Ссылка на инструкцию';
$string['settings/url_help_desc'] = 'По данной ссылке будет совершен переход при нажатии на кнопку';

// Страница экземпляра модуля
$string['view/link'] = 'Перейти к видеоконференции на mts-link.ru';

// Уведомления
$string['notification/error_accounts'] = 'Создание данного элемента курса невозможно, поскольку модуль не настроен:<br><b>Нет данных об используемых учетных записях mts-link.ru или имеются ошибки в JSON</b><br><br>Обратитесть к администратору!';
$string['notification/error_tokens'] = '<b>Указанная вами дата занята. Выберите другую дату проведения.</b><br><br>Данная ошибка вызвана ограниченным количеством учетных записей mts-link.ru<br>а также ограничением по количеству одновременно проводимых видеоконференций на каждом аккаунте.';
$string['notification/error_create_event'] = '<b>Не удалось создать мероприятие на mts-link.ru</b><br><br>Данная ошибка могла быть вызвана проблемами с сервисом mts-link.ru<br>попробуйте создать элемент курса снова.';
$string['notification/error_change_event'] = '<b>Не удалось редактировать мероприятие на mts-link.ru</b><br><br>Данная ошибка могла быть вызвана проблемами с сервисом mts-link.ru<br>попробуйте создать элемент курса снова.';

// AJAX
$string['ajax/desc_error_accounts'] = 'Невозможно проверить выбранный диапазон';
$string['ajax/desc_free_range'] = 'Выбранный диапазон времени свободен';
$string['ajax/desc_busy_range'] = 'Выбранный диапазон времени занят';
$string['ajax/error_accounts'] = 'Обратитесь к администратору. Данные об используемых учетных записях mts-link.ru недействительны.';
$string['ajax/label_teacher'] = 'Преподаватель';
$string['ajax/label_start_of'] = 'Начало';
$string['ajax/label_end_of'] = 'Окончание';
$string['ajax/legend_busy'] = 'Занятое время на каждой учетной записи mts-link.ru, доступной в рамках этого модуля (Всего: ';
$string['ajax/legend_selected'] = 'Выбранный диапазон времени';
$string['ajax/legend_selected_desc'] = 'Обратите внимание, что выбранный диапазон является свободным, если не имеет пересечений с занятыми для любой из доступных учетных записей mts-link.ru';

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
 * Языковые строки для плагина local_cdo_education_scoring.
 *
 * @package     local_cdo_education_scoring
 * @category    string
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'CDO Education Scoring - Анкеты качества';
$string['privacy:metadata'] = 'Плагин CDO Education Scoring не хранит персональные данные.';

// Capabilities.
$string['cdo_education_scoring:manage'] = 'Управление анкетами качества';
$string['cdo_education_scoring:view'] = 'Просмотр анкет качества';
$string['cdo_education_scoring:viewstats'] = 'Просмотр статистики сданных анкет';

// Errors.
$string['invalidjson'] = 'Неверные данные JSON';
$string['invalidaction'] = 'Неверное действие: {$a}';

// Общие.
$string['surveys'] = 'Анкеты качества';
$string['create_survey'] = 'Создать анкету';
$string['edit_survey'] = 'Редактировать анкету';
$string['delete_survey'] = 'Удалить анкету';
$string['activate'] = 'Активировать';
$string['deactivate'] = 'Деактивировать';
$string['active'] = 'Активна';
$string['inactive'] = 'Неактивна';
$string['edit'] = 'Редактировать';
$string['save'] = 'Сохранить';
$string['cancel'] = 'Отмена';
$string['close'] = 'Закрыть';
$string['delete'] = 'Удалить';
$string['add'] = 'Добавить';
$string['loading'] = 'Загрузка...';

// Анкеты.
$string['survey_title'] = 'Название анкеты';
$string['survey_description'] = 'Описание анкеты';
$string['survey_duration'] = 'Срок проведения опроса (дней)';
$string['survey_questions'] = 'Вопросы анкеты';
$string['survey_created'] = 'Создана';
$string['survey_status'] = 'Статус';
$string['survey_questions_count'] = 'Вопросов';
$string['survey_duration_days'] = 'Срок проведения';
$string['no_surveys'] = 'Анкет пока нет. Создайте первую анкету.';

// Вопросы.
$string['question'] = 'Вопрос';
$string['question_text'] = 'Текст вопроса';
$string['question_type'] = 'Тип ответа';
$string['question_type_scale'] = 'Балльная шкала (1-5)';
$string['question_type_text'] = 'Свободный ответ';
$string['add_question'] = 'Добавить вопрос';
$string['remove_question'] = 'Удалить вопрос';
$string['question_number'] = 'Вопрос {$a}';
$string['no_questions'] = 'Вопросов пока нет. Добавьте первый вопрос.';
$string['question_scale_info'] = 'Респонденты смогут выбрать оценку от 1 до 5 баллов.';
$string['question_text_info'] = 'Респонденты смогут ввести текстовый ответ.';

// Сообщения об успехе.
$string['survey_created_success'] = 'Анкета успешно создана';
$string['survey_updated_success'] = 'Анкета успешно обновлена';
$string['survey_activated_success'] = 'Анкета активирована';
$string['survey_deactivated_success'] = 'Анкета деактивирована';
$string['survey_deleted_success'] = 'Анкета успешно удалена';
$string['question_added_success'] = 'Вопрос успешно добавлен';

// Ошибки.
$string['error_load_surveys'] = 'Ошибка при загрузке анкет';
$string['error_create_survey'] = 'Ошибка при создании анкеты';
$string['error_update_survey'] = 'Ошибка при обновлении анкеты';
$string['error_activate_survey'] = 'Ошибка при активации анкеты';
$string['error_deactivate_survey'] = 'Ошибка при деактивации анкеты';
$string['error_delete_survey'] = 'Ошибка при удалении анкеты';
$string['error_survey_not_found'] = 'Анкета не найдена';
$string['error_invalid_survey_data'] = 'Неверные данные анкеты';
$string['error_invalid_question_data'] = 'Неверные данные вопроса';
$string['error_empty_title'] = 'Название анкеты не может быть пустым';
$string['error_invalid_duration'] = 'Срок проведения должен быть больше 0';
$string['error_no_questions'] = 'Анкета должна содержать хотя бы один вопрос';
$string['error_empty_question_text'] = 'Текст вопроса не может быть пустым';
$string['error_invalid_question_type'] = 'Неверный тип вопроса';

// Валидация.
$string['required_field'] = 'Обязательное поле';
$string['invalid_duration'] = 'Срок проведения должен быть положительным числом';
$string['invalid_question_type'] = 'Неверный тип вопроса. Допустимые значения: scale, text';

// Services (для описания web services).
$string['service_get_surveys_description'] = 'Получает список всех анкет качества';
$string['service_get_active_surveys_description'] = 'Получает список активных анкет для студентов';
$string['service_create_survey_description'] = 'Создает новую анкету качества';
$string['service_update_survey_description'] = 'Обновляет существующую анкету качества';
$string['service_activate_survey_description'] = 'Активирует или деактивирует анкету качества';

// Студенческий интерфейс.
$string['no_active_surveys'] = 'Активных анкет пока нет.';
$string['complete_survey'] = 'Заполнить анкету';
$string['view_survey'] = 'Просмотреть';
$string['survey_available'] = 'Доступна';
$string['survey_completed'] = 'Завершена';
$string['due_date'] = 'Срок сдачи';
$string['submit_answers'] = 'Отправить ответы';
$string['cancel'] = 'Отмена';
$string['question'] = 'Вопрос';
$string['required'] = 'Обязательно';
$string['enter_answer'] = 'Введите ваш ответ...';
$string['submit_success'] = 'Анкета успешно отправлена';
$string['submit_error'] = 'Ошибка при отправке ответов';
$string['fill_all_questions'] = 'Пожалуйста, заполните все обязательные поля';
$string['already_completed'] = 'Вы уже заполнили эту анкету';
$string['survey_expired'] = 'Срок действия анкеты истек';

// Настройки плагина.
$string['clear_tables'] = 'Очистка данных плагина';
$string['clear_tables_description'] = 'Эта операция удалит все данные из таблиц плагина (анкеты, вопросы, ответы). Действие необратимо!';
$string['clear_tables_button'] = 'Очистить все таблицы';
$string['clear_tables_confirm'] = 'Вы уверены, что хотите очистить все таблицы плагина? Это действие необратимо и удалит все анкеты, вопросы и ответы.';
$string['tables_cleared'] = 'Все таблицы плагина успешно очищены';

// Ссылка на страницу админов.
$string['admin_page'] = 'Страница управления';
$string['admin_page_description'] = 'Перейти на страницу управления анкетами качества';
$string['open_admin_page'] = 'Открыть страницу управления';


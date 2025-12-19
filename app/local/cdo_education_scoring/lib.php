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
 * Library functions for local_cdo_education_scoring plugin.
 *
 * @package     local_cdo_education_scoring
 * @category    lib
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Получение актуального имени таблицы (новое или старое, если миграция не выполнена).
 *
 * @param string $newTableName Новое имя таблицы
 * @param string $oldTableName Старое имя таблицы
 * @return string Актуальное имя таблицы
 * @throws \moodle_exception Если таблица не найдена
 */
function local_cdo_education_scoring_get_table_name(string $newTableName, string $oldTableName): string {
    global $DB;
    
    $dbman = $DB->get_manager();
    $table = new \xmldb_table($newTableName);
    
    if ($dbman->table_exists($table)) {
        return $newTableName;
    }
    
    // Пытаемся использовать старое имя таблицы
    $oldTable = new \xmldb_table($oldTableName);
    if ($dbman->table_exists($oldTable)) {
        debugging("Используется старое имя таблицы {$oldTableName}. Необходимо обновить плагин до версии 2024100110.", DEBUG_NORMAL);
        return $oldTableName;
    }
    
    throw new \moodle_exception('dmlreadexception', 'error', '', 
        "Таблица {$newTableName} не найдена. Убедитесь, что плагин установлен и обновлен до последней версии (2024100110).");
}

/**
 * Проверка прав администратора для управления анкетами.
 *
 * @return bool True если пользователь имеет права администратора
 */
function local_cdo_education_scoring_can_manage(): bool {
    $context = \context_system::instance();
    return has_capability('local/cdo_education_scoring:manage', $context);
}

/**
 * Проверка прав пользователя для просмотра анкет.
 *
 * @return bool True если пользователь имеет права просмотра
 */
function local_cdo_education_scoring_can_view(): bool {
    $context = \context_system::instance();
    return has_capability('local/cdo_education_scoring:view', $context);
}

/**
 * Получение списка анкет.
 *
 * @param bool $activeonly Только активные анкеты
 * @return array Массив анкет
 */
function local_cdo_education_scoring_get_surveys(bool $activeonly = false): array {
    global $DB;

    $tableName = local_cdo_education_scoring_get_table_name(
        'local_cdo_edu_score_survey',
        'local_cdo_education_scoring_survey'
    );

    $conditions = [];
    if ($activeonly) {
        $conditions['isactive'] = 1;
    }

    return $DB->get_records($tableName, $conditions, 'timecreated DESC');
}

/**
 * Получение анкеты по ID.
 *
 * @param int $surveyid ID анкеты
 * @return \stdClass|null Объект анкеты или null
 */
function local_cdo_education_scoring_get_survey(int $surveyid): ?\stdClass {
    global $DB;
    
    $tableName = local_cdo_education_scoring_get_table_name(
        'local_cdo_edu_score_survey',
        'local_cdo_education_scoring_survey'
    );
    
    return $DB->get_record($tableName, ['id' => $surveyid]) ?: null;
}

/**
 * Получение вопросов анкеты.
 *
 * @param int $surveyid ID анкеты
 * @return array Массив вопросов
 */
function local_cdo_education_scoring_get_questions(int $surveyid): array {
    global $DB;
    
    $tableName = local_cdo_education_scoring_get_table_name(
        'local_cdo_edu_score_quest',
        'local_cdo_education_scoring_question'
    );
    
    return $DB->get_records(
        $tableName,
        ['surveyid' => $surveyid],
        'sortorder ASC'
    );
}

/**
 * Форматирует полное имя пользователя с фамилией в начале.
 * Формат: Фамилия Имя Отчество
 *
 * @param stdClass $user Объект пользователя
 * @return string Полное имя в формате "Фамилия Имя Отчество"
 */
function local_cdo_education_scoring_format_fullname($user): string {
    if (!$user) {
        return '';
    }
    
    $parts = [];
    
    // Фамилия (lastname)
    if (!empty($user->lastname)) {
        $parts[] = trim($user->lastname);
    }
    
    // Имя (firstname)
    if (!empty($user->firstname)) {
        $parts[] = trim($user->firstname);
    }
    
    // Отчество (middlename) - если есть
    if (!empty($user->middlename)) {
        $parts[] = trim($user->middlename);
    }
    
    return implode(' ', $parts);
}

/**
 * Добавляет ссылку на опрос в главное навигационное меню.
 *
 * @param global_navigation $navigation Объект глобальной навигации
 */
function local_cdo_education_scoring_extend_navigation(global_navigation $navigation) {
    global $USER;
    
    // Проверяем что пользователь авторизован
    if (!isloggedin() || isguestuser()) {
        return;
    }
    
    $context = context_system::instance();
    
    // Определяем URL в зависимости от роли пользователя
    $url = new moodle_url('/local/cdo_education_scoring/index.php');
    
    // Определяем название ссылки
    $linktext = get_string('pluginname', 'local_cdo_education_scoring');
    
    // Добавляем узел в главное меню
    $node = $navigation->add(
        $linktext,
        $url,
        navigation_node::TYPE_CUSTOM,
        null,
        'cdo_education_scoring',
        new pix_icon('i/report', '')
    );
    
    // Делаем узел видимым
    if ($node) {
        $node->showinflatnavigation = true;
    }
}

/**
 * Добавляет ссылку на опрос в навигацию пользователя.
 *
 * @param navigation_node $navigation Корневой узел навигации
 * @param stdClass $user Объект пользователя
 * @param context_user $usercontext Контекст пользователя
 * @param stdClass $course Объект курса
 * @param context_course $coursecontext Контекст курса
 */
function local_cdo_education_scoring_extend_navigation_user($navigation, $user, $usercontext, $course, $coursecontext) {
    global $USER;
    
    // Проверяем что это текущий пользователь
    if ($USER->id != $user->id) {
        return;
    }
    
    // Проверяем что пользователь авторизован
    if (!isloggedin() || isguestuser()) {
        return;
    }
    
    // URL страницы опроса
    $url = new moodle_url('/local/cdo_education_scoring/index.php');
    
    // Название ссылки
    $linktext = get_string('pluginname', 'local_cdo_education_scoring');
    
    // Добавляем узел
    $node = $navigation->add(
        $linktext,
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'cdo_education_scoring',
        new pix_icon('i/report', '')
    );
}


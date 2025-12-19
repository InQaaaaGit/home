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
 * Определение external API функций для плагина local_cdo_ok.
 *
 * @package    local_cdo_ok
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_cdo_ok\external\external_active_groups;
use local_cdo_ok\external\external_answers;
use local_cdo_ok\external\external_confirm_answers;
use local_cdo_ok\external\external_questions;

defined('MOODLE_INTERNAL') || die();

$functions = [
    // Функции для работы с вопросами.
    'local_cdo_ok_get_questions' => [
        'classname' => external_questions::class,
        'methodname' => 'get_questions',
        'description' => 'Получает все вопросы, заведенные в плагине',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],
    'local_cdo_ok_create_question' => [
        'classname' => external_questions::class,
        'methodname' => 'create_question',
        'description' => 'Создает новый вопрос в БД',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],
    'local_cdo_ok_update_question' => [
        'classname' => external_questions::class,
        'methodname' => 'update_question',
        'description' => 'Обновляет существующий вопрос в БД',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],
    'local_cdo_ok_delete_question' => [
        'classname' => external_questions::class,
        'methodname' => 'delete_question',
        'description' => 'Удаляет вопрос из БД',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],
    'local_cdo_ok_update_questions' => [
        'classname' => external_questions::class,
        'methodname' => 'update_questions',
        'description' => 'Массово обновляет вопросы в БД',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],

    // Функции для работы с ответами.
    'local_cdo_ok_create_answer' => [
        'classname' => external_answers::class,
        'methodname' => 'create_answer',
        'description' => 'Создает новый ответ на вопрос',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],
    'local_cdo_ok_get_question_with_answers' => [
        'classname' => external_answers::class,
        'methodname' => 'get_question_with_answers',
        'description' => 'Получает вопросы с ответами пользователей',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],

    // Функции для работы с активными группами.
    'local_cdo_ok_active_groups_create_update' => [
        'classname' => external_active_groups::class,
        'methodname' => 'create_update',
        'description' => 'Создает или обновляет статус активности для анкеты',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],
    'local_cdo_ok_active_groups_get_active_group' => [
        'classname' => external_active_groups::class,
        'methodname' => 'get_active_group',
        'description' => 'Получает статус активности анкеты',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],

    // Функции для работы с подтверждением ответов.
    'local_cdo_ok_confirm_answers_create_update' => [
        'classname' => external_confirm_answers::class,
        'methodname' => 'create_update_confirm_answer',
        'description' => 'Создает или обновляет текущий статус подтверждения ответов в анкете',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],
    'local_cdo_ok_confirm_answers_get_confirm_answer' => [
        'classname' => external_confirm_answers::class,
        'methodname' => 'get_confirm_answer',
        'description' => 'Получает информацию о подтверждении ответов',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
        'loginrequired' => true,
    ],
];
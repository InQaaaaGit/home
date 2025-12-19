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
 * External services definition for local_cdo_education_scoring plugin.
 *
 * @package     local_cdo_education_scoring
 * @category    external
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_cdo_education_scoring\external\get_surveys;
use local_cdo_education_scoring\external\get_active_surveys;
use local_cdo_education_scoring\external\create_survey;
use local_cdo_education_scoring\external\update_survey;
use local_cdo_education_scoring\external\activate_survey;
use local_cdo_education_scoring\external\submit_survey_response;
use local_cdo_education_scoring\external\get_teachers;
use local_cdo_education_scoring\external\check_survey_availability;
use local_cdo_education_scoring\external\get_students_report;
use local_cdo_education_scoring\external\get_teachers_for_report;
use local_cdo_education_scoring\external\get_survey_stats;
use local_cdo_education_scoring\external\get_percent_attendance;

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_cdo_education_scoring_get_surveys' => [
        'classname' => get_surveys::class,
        'methodname' => 'execute',
        'description' => 'Получает список всех анкет качества',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_cdo_education_scoring_get_active_surveys' => [
        'classname' => get_active_surveys::class,
        'methodname' => 'execute',
        'description' => 'Получает список активных анкет для студентов',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_cdo_education_scoring_create_survey' => [
        'classname' => create_survey::class,
        'methodname' => 'execute',
        'description' => 'Создает новую анкету качества',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_cdo_education_scoring_update_survey' => [
        'classname' => update_survey::class,
        'methodname' => 'execute',
        'description' => 'Обновляет существующую анкету качества',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_cdo_education_scoring_activate_survey' => [
        'classname' => activate_survey::class,
        'methodname' => 'execute',
        'description' => 'Активирует или деактивирует анкету качества',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_cdo_education_scoring_submit_survey_response' => [
        'classname' => submit_survey_response::class,
        'methodname' => 'execute',
        'description' => 'Отправляет ответы на анкету',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_cdo_education_scoring_get_teachers' => [
        'classname' => get_teachers::class,
        'methodname' => 'execute',
        'description' => 'Получает список преподавателей по дисциплине',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_cdo_education_scoring_check_survey_availability' => [
        'classname' => check_survey_availability::class,
        'methodname' => 'execute',
        'description' => 'Проверяет условия доступности анкеты для пользователя',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_cdo_education_scoring_get_students_report' => [
        'classname' => get_students_report::class,
        'methodname' => 'execute',
        'description' => 'Получает данные студентов для отчета',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_cdo_education_scoring_get_teachers_for_report' => [
        'classname' => get_teachers_for_report::class,
        'methodname' => 'execute',
        'description' => 'Получает список преподавателей на основе сданных анкет',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_cdo_education_scoring_get_survey_stats' => [
        'classname' => get_survey_stats::class,
        'methodname' => 'execute',
        'description' => 'Получает статистику сданных анкет по преподавателю',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_cdo_education_scoring_get_percent_attendance' => [
        'classname' => get_percent_attendance::class,
        'methodname' => 'execute',
        'description' => 'Получает процент посещаемости студента по дисциплине',
        'type' => 'read',
        'ajax' => true,
    ],
];

$services = [
    'cdo_education_scoring_services' => [
        'functions' => [
            'local_cdo_education_scoring_get_surveys',
            'local_cdo_education_scoring_create_survey',
            'local_cdo_education_scoring_update_survey',
            'local_cdo_education_scoring_activate_survey',
            'local_cdo_education_scoring_get_students_report',
            'local_cdo_education_scoring_get_teachers_for_report',
            'local_cdo_education_scoring_get_percent_attendance',
        ],
        'requiredcapability' => 'local/cdo_education_scoring:manage',
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
    'cdo_education_scoring_student_services' => [
        'functions' => [
            'local_cdo_education_scoring_get_active_surveys',
            'local_cdo_education_scoring_submit_survey_response',
            'local_cdo_education_scoring_get_teachers',
            'local_cdo_education_scoring_check_survey_availability',
        ],
        'requiredcapability' => 'local/cdo_education_scoring:view',
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
    'cdo_education_scoring_teacher_services' => [
        'functions' => [
            'local_cdo_education_scoring_get_survey_stats',
        ],
        'requiredcapability' => 'local/cdo_education_scoring:viewstats',
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];


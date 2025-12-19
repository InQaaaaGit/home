<?php

defined('MOODLE_INTERNAL') || die();

use tool_cdo_config\external\add_to_course;
use tool_cdo_config\external\authorization;
use tool_cdo_config\external\cohorts;
use tool_cdo_config\external\course_sections;
use tool_cdo_config\external\download_file_submissions;
use tool_cdo_config\external\enrol;
use tool_cdo_config\external\grades;
use tool_cdo_config\external\gradereports;
use tool_cdo_config\external\roles;
use tool_cdo_config\external\users;

$functions = [
    'cdo_user_update' => [
        'classname' => users::class,
        'methodname' => 'user_update',
        'description' => '',
        'type' => 'write'
    ],
    'create_demo_account' => [
        'classname' => users::class,
        'methodname' => 'create_demo_account',
        'description' => '',
        'type' => 'write'
    ],
    'download_file_submission' => [
        'classname' => download_file_submissions::class,
        'methodname' => 'download_file_submission',
        'description' => 'Получить файлы ответов заданий пользователя',
        'type' => 'write'
    ],
    'gradereport_user_get_grade_items_cdo' => [
        'classname' => gradereports::class,
        'methodname' => 'get_grade_items',
        'description' => '',
        'type' => 'write'
    ],
    'create_section' => [
        'classname' => course_sections::class,
        'methodname' => 'create_course_section',
        'description' => 'Создать тему внутри курса.',
        'type' => 'write'
    ],
    'get_section' => [
        'classname' => course_sections::class,
        'methodname' => 'get_course_section',
        'description' => 'Получить информацию по секции.',
        'type' => 'write'
    ],
    'update_section' => [
        'classname' => course_sections::class,
        'methodname' => 'update_section',
        'description' => 'Обновить информацию по секции.',
        'type' => 'write'
    ],
    'get_eios_roles' => [
        'classname' => roles::class,
        'methodname' => 'get_eios_roles',
        'description' => 'Получить список ролей созданных для корректной работы расширений ЭИОС',
        'type' => 'read'
    ],
    'unenrol_cohort_from_course' => [
        'classname' => cohorts::class,
        'methodname' => 'unenrol_cohort_from_course',
        'description' => 'Удаляет метод записи "синхронизация с глобальной группой" с курса(course))',
        'type' => 'read'
    ],
    'enrol_cohort_on_course' => [
        'classname' => cohorts::class,
        'methodname' => 'enrol_cohort',
        'description' => 'Добавляет метод записи "синхронизация с глобальной группой" на курса(course))',
        'type' => 'read'
    ],
    'authorized_user' => [
        'classname' => authorization::class,
        'methodname' => 'authorized_users',
        'description' => 'Попытка авторизация из мобильного приложения',
        'type' => 'read'
    ],
    'add_page_to_course' => [
        'classname' => add_to_course::class,
        'methodname' => 'add_page',
        'description' => 'Добавляет модуль Страница в курс по секции ид',
        'type' => 'read'
    ],
    'cdo_enrol_get_users_courses' => [
        'classname' => enrol::class,
        'methodname' => 'enrol_get_users_courses',
        'description' => 'Аналогичная функция из ядра, с новыми реквизитами возврата',
        'type' => 'read',
        'capabilities' => 'moodle/course:viewparticipants',
        'ajax' => true,
    ],
    'set_grade_to_first_assign_in_section' => [
        'classname' => grades::class,
        'methodname' => 'set_grade_to_first_assign_in_section',
        'description' => 'Выставляет оценку в первый assignment, найденный в указанной секции',
        'type' => 'write',
        'capabilities' => 'mod/assign:grade',
        'ajax' => true,
    ],
    'create_flexible_section' => [
        'classname' => course_sections::class,
        'methodname' => 'flexible_section_create',
        'description' => 'Создать тему внутри курса c форматом flexible',
        'type' => 'write'
    ],
    'get_modules_in_section' => [
        'classname' => course_sections::class,
        'methodname' => 'get_modules_in_section',
        'description' => 'получить ID модулей, находящихся внутри темы',
        'type' => 'write'
    ],
    'delete_flexible_section' => [
        'classname' => course_sections::class,
        'methodname' => 'flexible_section_delete',
        'description' => 'Удалить секцию и все дочерние элементы в формате flexsections',
        'type' => 'write',
        'capabilities' => 'moodle/course:update',
        'ajax' => true,
    ],
    'delete_section' => [
        'classname' => course_sections::class,
        'methodname' => 'section_delete',
        'description' => 'Удалить обычную секцию из курса с форматом sections/topics/weekly',
        'type' => 'write',
        'capabilities' => 'moodle/course:update',
        'ajax' => true,
    ],
];

$services = [
    'cdo_default_integration' => [
        'functions' => [
            'cdo_enrol_get_users_courses',
            'download_file_submission',
            'core_cohort_add_cohort_members',
            'core_cohort_create_cohorts',
            'core_cohort_delete_cohort_members',
            'core_cohort_search_cohorts',
            'core_cohort_get_cohort_members',
            'core_course_create_categories',
            'core_course_create_courses',
            'core_course_get_categories',
            'core_course_get_courses',
            'core_course_get_courses_by_field',
            'core_course_update_categories',
            'core_course_update_courses',
            'core_role_assign_roles',
            'core_user_create_users',
            'core_user_get_users',
            'core_user_update_users',
            'core_user_delete_users',
            'create_section',
            'update_section',
            'add_page_to_course',
            'enrol_manual_enrol_users',
            'enrol_cohort_on_course',
            'tool_lp_search_cohorts',
            'get_eios_roles',
            'create_flexible_section',
            'gradereport_overview_get_course_grades',
            'get_modules_in_section',
            'enrol_manual_unenrol_users',
            'gradereport_user_get_grade_items',
            'core_calendar_delete_calendar_events',
            'core_calendar_create_calendar_events',
            'core_group_add_group_members',
            'core_group_create_groups',
            'core_group_delete_group_members',
            'core_group_get_course_groups',
            'core_comment_get_comments',
            'gradereport_user_get_grade_items_cdo',
            'core_course_delete_courses',
            'create_demo_account',
            'core_grades_update_grades',
            'gradereport_overview_get_course_grades',
            'cdo_user_update',
            'set_grade_to_first_assign_in_section',
            'local_cdo_ag_tools_set_category_and_course_grades',
            'delete_flexible_section',
            'delete_section',
        ],
        //'requiredcapability' => '',
        //'restrictedusers' => 0,
        'enabled' => 1,
    ]
];

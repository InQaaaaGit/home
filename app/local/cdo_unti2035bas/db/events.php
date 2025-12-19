<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '*',
        'callback' => 'local_cdo_unti2035bas\observer\course_controller::all_events',
        'internal' => false,
    ],
    [
        'eventname' => 'core\event\course_section_updated',
        'callback' => 'local_cdo_unti2035bas\observer\course_controller::event_section_updated',
        'internal' => false,
    ],
    // События для отслеживания активности с контентом
    [
        'eventname' => 'mod_resource\event\course_module_viewed',
        'callback' => 'local_cdo_unti2035bas\observer\content_activity_controller::resource_viewed',
        'internal' => false,
    ],
    [
        'eventname' => 'mod_page\event\course_module_viewed',
        'callback' => 'local_cdo_unti2035bas\observer\content_activity_controller::page_viewed',
        'internal' => false,
    ],
    [
        'eventname' => 'mod_url\event\course_module_viewed',
        'callback' => 'local_cdo_unti2035bas\observer\content_activity_controller::resource_viewed',
        'internal' => false,
    ],
    [
        'eventname' => 'core\event\course_module_viewed',
        'callback' => 'local_cdo_unti2035bas\observer\content_activity_controller::resource_viewed',
        'internal' => false,
    ],
    // События для скачивания файлов
    [
        'eventname' => 'core\event\file_downloaded',
        'callback' => 'local_cdo_unti2035bas\observer\content_activity_controller::file_downloaded',
        'internal' => false,
    ],
    [
        'eventname' => 'mod_resource\event\file_downloaded',
        'callback' => 'local_cdo_unti2035bas\observer\content_activity_controller::file_downloaded',
        'internal' => false,
    ],
    // События для отслеживания выставления оценок
    [
        'eventname' => '\core\event\user_graded',
        'callback' => '\local_cdo_unti2035bas\observer\grade_observer::user_graded',
        'internal' => false,
    ],
    [
        'eventname' => '\core\event\grade_deleted',
        'callback' => '\local_cdo_unti2035bas\observer\grade_observer::grade_deleted',
        'internal' => false,
    ],
    [
        'eventname' => '\core\event\grade_item_created',
        'callback' => '\local_cdo_unti2035bas\observer\grade_observer::grade_item_created',
        'internal' => false,
    ],
    [
        'eventname' => '\core\event\grade_item_updated',
        'callback' => '\local_cdo_unti2035bas\observer\grade_observer::grade_item_updated',
        'internal' => false,
    ],
    [
        'eventname' => '\core\event\grade_item_deleted',
        'callback' => '\local_cdo_unti2035bas\observer\grade_observer::grade_item_deleted',
        'internal' => false,
    ],
    [
        'eventname' => '\mod_quiz\event\attempt_submitted',
        'callback' => '\local_cdo_unti2035bas\observer\grade_observer::quiz_attempt_submitted',
        'internal' => false,
    ],
];

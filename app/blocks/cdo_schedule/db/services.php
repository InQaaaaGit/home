<?php

use block_cdo_schedule\external\external_block_cdo_schedule;
use block_cdo_schedule\external\external_filters;

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_cdo_schedule_get_schedule_data' => [
        'classname' => external_block_cdo_schedule::class,
        'methodname' => 'get_full_schedule',
        'description' => '',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => false
    ],
    'block_cdo_schedule_get_courses' => [
        'classname' => external_filters::class,
        'methodname' => 'get_courses',
        'description' => 'Get list of courses',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false
    ],
    'block_cdo_schedule_get_groups' => [
        'classname' => external_filters::class,
        'methodname' => 'get_groups',
        'description' => 'Get list of groups by course',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false
    ],
    'block_cdo_schedule_get_set_attendance' => [
        'classname' => external_block_cdo_schedule::class,
        'methodname' => 'get_set_attendance',
        'description' => 'Gets or sets attendance data',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true  // Посещаемость требует авторизации
    ],
    'block_cdo_schedule_set_attendance' => [
        'classname'   => external_block_cdo_schedule::class,
        'methodname'  => 'set_attendance',
        'classpath'   => 'blocks/cdo_schedule/classes/external/external_block_cdo_schedule.php',
        'description' => 'Sets attendance data',
        'type'        => 'write',
        'ajax'        => true,
        'loginrequired' => true  // Установка посещаемости требует авторизации
    ],
    'block_cdo_schedule_get_full_schedule' => [ //
        'classname' => external_block_cdo_schedule::class,
        'methodname' => 'get_schedule_data',
        'description' => 'Get full schedule data with detailed information',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false
    ],
];

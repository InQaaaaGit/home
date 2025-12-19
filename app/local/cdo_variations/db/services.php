<?php

use local_cdo_variations\external\general;

defined('MOODLE_INTERNAL') || die();

$functions = [
    'cdo_core_course_get_contents' => [
        'classname' => general::class,
        'methodname' => 'get_course_contents',
        'description' => 'core_course_get_contents',
        'type' => 'read',
        'ajax' => true
    ],
    'cdo_update_module_availability_info' => [
        'classname' => general::class,
        'methodname' => 'update_module_availability_info',
        'description' => 'update_module_availability_info',
        'type' => 'read',
        'ajax' => true,
    ],
    'cdo_get_user_variations' => [
        'classname' => general::class,
        'methodname' => 'get_user_variations',
        'description' => 'get_user_variations',
        'type' => 'read',
        'ajax' => true,
    ],
];
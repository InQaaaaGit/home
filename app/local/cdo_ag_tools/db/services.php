<?php

use local_cdo_ag_tools\external\availability;
use local_cdo_ag_tools\external\grades;

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_cdo_ag_tools_get_users' => [
        'classname' => availability::class,
        'methodname' => 'get_users',
        'description' => 'Returns a list of users.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_cdo_ag_tools_set_availability' => [
        'classname' => availability::class,
        'methodname' => 'set_availability',
        'description' => 'Sets availability for course sections between specified quarters for a given user',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_cdo_ag_tools_set_grade_to_first_assign_in_section' => [
        'classname' => grades::class,
        'methodname' => 'set_grade_to_first_assign_in_section',
        'description' => 'Sets grade to the first assignment found in the specified course section',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/assign:grade',
    ],


];
$services = [
    'cdo_ag_tools_API' => [
        'functions' => [

        ],
        'enabled' => 1,
    ]
];

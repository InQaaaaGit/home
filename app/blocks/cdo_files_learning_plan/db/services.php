<?php

use block_cdo_files_learning_plan\external\main_opop;

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_files_learning_plan_set_agreed' => [
        'classname' => main_opop::class,
        'methodname' => 'set_agreed',
        'description' => '',
        'type' => 'write',
        'ajax' => true
    ],
];
<?php

defined('MOODLE_INTERNAL') || die();

$messageproviders = [
    'grade_update' => [
        'defaults' => [
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ],
    ],
    'work_uploaded' => [
        'defaults' => [
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ],
    ],
    'work_graded' => [
        'defaults' => [
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ],
    ],
    'weekly_quiz_report' => [
        'defaults' => [
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ],
    ],
]; 
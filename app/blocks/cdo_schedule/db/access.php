<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'block/cdo_schedule:viewstudentschedule' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'block/cdo_schedule:viewteacherschedule' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'block/cdo_schedule:myaddinstance' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ],
    'block/cdo_schedule:addinstance' => [
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
    ]
];

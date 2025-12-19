<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/cdo_rpd:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_rpd:view_admin_rpd' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_rpd:view_rpd' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_rpd:view_admin_library' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_rpd:view_worker_library' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_rpd:view_executive_secretary' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ]
];
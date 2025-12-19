<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/cdo_academic_progress:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_academic_progress:view_rpd' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_academic_progress:view_admin_rpd' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM
    ]
];

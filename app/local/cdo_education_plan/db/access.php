<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/cdo_education_plan:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_education_plan:view_rpd' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_education_plan:view_admin_rpd' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM
    ]
];
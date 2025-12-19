<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/cdo_certification_sheet:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_certification_sheet:view_rpd' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_certification_sheet:view_admin_rpd' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM
    ]
];

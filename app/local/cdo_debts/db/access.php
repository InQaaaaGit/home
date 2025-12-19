<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/cdo_debts:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_debts:view_rpd' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_debts:view_admin_rpd' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM
    ]
];
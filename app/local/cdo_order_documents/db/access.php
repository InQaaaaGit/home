<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/cdo_order_documents:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_order_documents:view_rpd' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_order_documents:view_admin_rpd' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_order_documents:student_view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_order_documents:aspirant_view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ]
];
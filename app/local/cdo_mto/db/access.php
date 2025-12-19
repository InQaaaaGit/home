<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/cdo_mto:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ]
];

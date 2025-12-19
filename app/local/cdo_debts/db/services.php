<?php

use local_cdo_debts\external\external_debts;

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_cdo_debts_get_academic_debts' => [
        'classname' => external_debts::class,
        'methodname' => 'get_academic_debts',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_debts_send_request_retake' => [
        'classname' => external_debts::class,
        'methodname' => 'create_request_retake',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_debts_get_retake_list_by_user_id' => [
        'classname' => external_debts::class,
        'methodname' => 'get_retake_list_by_user_id',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_debts_update_status_retake' => [
        'classname' => external_debts::class,
        'methodname' => 'update_status_retake',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
];
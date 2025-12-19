<?php

use local_cdo_vkr\external\work_with_files;
use local_cdo_vkr\external\work_with_VKR;

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_cdo_vkr_save_file' => [
        'classname' => work_with_files::class,
        'methodname' => 'save_file',
        'description' => '',
        'type' => 'write',
        'ajax' => true
    ],
    'local_cdo_vkr_get_file' => [
        'classname' => work_with_files::class,
        'methodname' => 'get_file',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_get_files' => [
        'classname' => work_with_files::class,
        'methodname' => 'get_files',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_delete_file' => [
        'classname' => work_with_files::class,
        'methodname' => 'delete_file',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_change_status_vkr' => [
        'classname' => work_with_VKR::class,
        'methodname' => 'change_status_of_vkr',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_get_vkrs' => [
        'classname' => work_with_VKR::class,
        'methodname' => 'get_vkrs_by_user',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_get_vkr_info' => [
        'classname' => work_with_VKR::class,
        'methodname' => 'get_vkr_info_by_student',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_accept_ebs_agreed' => [
        'classname' => work_with_VKR::class,
        'methodname' => 'accept_EBS_agreed',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_set_acquainted_status' => [
        'classname' => work_with_files::class,
        'methodname' => 'set_acquainted_status',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_push_work_to_archive' => [
        'classname' => work_with_files::class,
        'methodname' => 'push_work_to_archive',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_change_manager_status_of_vkr' => [
        'classname' => work_with_VKR::class,
        'methodname' => 'change_manager_status_of_vkr',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_set_acquainted_to_vkr' => [
        'classname' => work_with_VKR::class,
        'methodname' => 'set_acquainted_to_vkr',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_activate_process_placing_vkr_into_ebs' => [
        'classname' => work_with_VKR::class,
        'methodname' => 'activate_process_placing_vkr_into_ebs',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_delete_vkr_entirely' => [
        'classname' => work_with_VKR::class,
        'methodname' => 'delete_vkr_entirely',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
    'local_cdo_vkr_check_is_gek' => [
        'classname' => work_with_VKR::class,
        'methodname' => 'check_is_gek',
        'description' => '',
        'type' => 'read',
        'ajax' => true
    ],
];

$services = [
    'cdo_vkr_services' => [
        'functions' => [
            'local_cdo_vkr_activate_process_placing_vkr_into_ebs'
        ],
        'requiredcapability' => '',
        'restrictedusers' => 0,
        'enabled' => 1
    ]
];
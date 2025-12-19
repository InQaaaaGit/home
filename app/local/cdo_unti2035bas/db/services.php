<?php

$functions = [
    'local_cdo_unti2035bas_stream_sync' => [
        'classname' => 'local_cdo_unti2035bas\external\stream_sync',
        'description' => 'Stream sync from moodle',
        'type' => 'write',
        'ajax' => true,
    ],

    'local_cdo_unti2035bas_stream_fd_add' => [
        'classname' => 'local_cdo_unti2035bas\external\stream_fd_add',
        'description' => 'Stream add fd extension',
        'type' => 'write',
        'ajax' => true,
    ],

    'local_cdo_unti2035bas_stream_fd_delete' => [
        'classname' => 'local_cdo_unti2035bas\external\stream_fd_delete',
        'description' => 'Stream delete fd extension',
        'type' => 'write',
        'ajax' => true,
    ],

    'local_cdo_unti2035bas_factdef_extension_delete' => [
        'classname' => 'local_cdo_unti2035bas\external\factdef_extension_delete',
        'description' => 'Factdef delete extension',
        'type' => 'write',
        'ajax' => true,
    ],

    'local_cdo_unti2035bas_fact_delete' => [
        'classname' => 'local_cdo_unti2035bas\external\fact_delete',
        'description' => 'Fact delete',
        'type' => 'write',
        'ajax' => true,
    ],

    'local_cdo_unti2035bas_fact_extension_delete' => [
        'classname' => 'local_cdo_unti2035bas\external\fact_extension_delete',
        'description' => 'Fact delete extension',
        'type' => 'write',
        'ajax' => true,
    ],

    'local_cdo_unti2035bas_practice_diary_delete' => [
        'classname' => 'local_cdo_unti2035bas\external\practice_diary_delete',
        'description' => 'Practice diary delete',
        'type' => 'write',
        'ajax' => true,
    ],
];

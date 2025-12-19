<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/cdo_vkr:view' => [ //возможность видеть плагин
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_vrk:user_teacher' => [ //пользователь ППС и имеет свой доступ
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ],
    'local/cdo_vrk:user_study' => [ //пользователь обучающийся и имеет свой доступ
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM
    ]
];

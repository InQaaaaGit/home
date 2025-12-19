<?php

defined('MOODLE_INTERNAL') || die();

    $capabilities = array(
        'block/cdo_professional_info:view' => [
            'captype' => 'read',
            'contextlevel' => CONTEXT_SYSTEM
        ],

        'block/cdo_professional_info:myaddinstance' => array(
            'captype' => 'write',
            'contextlevel' => CONTEXT_SYSTEM,
            'archetypes' => array(
                'guest' => CAP_ALLOW,
                'student' => CAP_ALLOW,
                'teacher' => CAP_ALLOW,
                'editingteacher' => CAP_ALLOW,
                'coursecreator' => CAP_ALLOW,
                'manager' => CAP_ALLOW
            ),
            'clonepermissionsfrom' => 'moodle/my:manageblocks'
        ),

        'block/cdo_professional_info:addinstance' => array(
            'riskbitmask' => RISK_SPAM | RISK_XSS,
            'captype' => 'write',
            'contextlevel' => CONTEXT_BLOCK,
            'archetypes' => array(
                'user' => CAP_ALLOW,
                'student' => CAP_ALLOW,
                'teacher' => CAP_ALLOW,
                'editingteacher' => CAP_ALLOW,
                'coursecreator' => CAP_ALLOW,
                'manager' => CAP_ALLOW
            ),
        ),

    );

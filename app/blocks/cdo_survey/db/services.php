<?php

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_cdo_survey_submit_survey' => [
        'classname'   => 'block_cdo_survey\external',
        'methodname'  => 'submit_survey',
        'classpath'   => 'blocks/cdo_survey/classes/external.php',
        'description' => 'Submits survey data',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'blocks_cdo_survey_get_citizenship' => [
        'classname'   => 'block_cdo_survey\external',
        'methodname'  => 'get_citizenship',
        'classpath'   => 'blocks/cdo_survey/classes/external.php',
        'description' => 'Returns citizenship data.',
        'type'        => 'read',
        'ajax'        => true,
    ],
    'block_cdo_survey_get_education_levels' => [
        'classname'   => 'block_cdo_survey\external',
        'methodname'  => 'get_education_levels',
        'classpath'   => 'blocks/cdo_survey/classes/external.php',
        'description' => 'Returns education levels data.',
        'type'        => 'read',
        'ajax'        => true,
    ],
    'block_cdo_survey_get_user_groups' => [
        'classname'   => 'block_cdo_survey\external',
        'methodname'  => 'get_user_groups',
        'classpath'   => 'blocks/cdo_survey/classes/external.php',
        'description' => 'Returns user groups data.',
        'type'        => 'read',
        'ajax'        => true,
    ],
    'block_cdo_survey_get_course_schedule' => [
        'classname'   => 'block_cdo_survey\external',
        'methodname'  => 'get_course_schedule',
        'classpath'   => 'blocks/cdo_survey/classes/external.php',
        'description' => '',
        'type'        => 'read',
        'ajax'        => true,
    ],
     'block_cdo_survey_get_identity_document_types' => [
        'classname'   => 'block_cdo_survey\\external',
        'methodname'  => 'get_identity_document_types',
        'classpath'   => 'blocks/cdo_survey/classes/external.php',
        'description' => 'Returns identity document types data.',
        'type'        => 'read',
        'ajax'        => true,
    ],
    'block_cdo_survey_get_survey_data' => [
        'classname'   => 'block_cdo_survey\\external',
        'methodname'  => 'get_survey_data',
        'classpath'   => 'blocks/cdo_survey/classes/external.php',
        'description' => 'Returns previously submitted survey data for the current user.',
        'type'        => 'read',
        'ajax'        => true,
    ],
];

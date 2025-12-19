<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\user_graded',
        'callback' => 'local_cdo_reserve\observers\grade_update::observe_grade_updated',
        'priority' => 1000,
        'internal' => false,
    ],
   /* [
        'eventname' => '\core\event\course_created',
        'callback' => 'local_cdo_ag_tools\observers\grade_update::observe_grade_updated',
    ],*/
];
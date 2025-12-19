<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    // Существующий observer для user_graded
    [
        'eventname' => '\core\event\user_graded',
        'callback' => 'local_cdo_ag_tools\observers\grade_update::observe_grade_updated',
    ],
    
    // Новые observers для grade interceptor
    [
        'eventname' => '\core\event\user_graded',
        'callback' => 'local_cdo_ag_tools\observers\grade_interceptor::observe_user_graded',
    ],
    [
        'eventname' => '\core\event\grade_item_updated',
        'callback' => 'local_cdo_ag_tools\observers\grade_interceptor::observe_grade_item_updated',
    ],
    [
        'eventname' => '\mod_assign\event\submission_graded',
        'callback' => 'local_cdo_ag_tools\observers\grade_interceptor::observe_submission_graded',
    ],
    
    // Observers для уведомлений о работах
    [
        'eventname' => '\mod_assign\event\submission_created',
        'callback' => 'local_cdo_ag_tools\observers\work_notification_observer::observe_submission_created',
    ],
    [
        'eventname' => '\mod_assign\event\submission_updated',
        'callback' => 'local_cdo_ag_tools\observers\work_notification_observer::observe_submission_updated',
    ],
    [
        'eventname' => '\mod_assign\event\assessable_submitted',
        'callback' => 'local_cdo_ag_tools\observers\work_notification_observer::observe_assessable_submitted',
    ],
    [
        'eventname' => '\mod_assign\event\submission_graded',
        'callback' => 'local_cdo_ag_tools\observers\work_notification_observer::observe_work_graded',
    ],
    
   /* [
        'eventname' => '\core\event\course_created',
        'callback' => 'local_cdo_ag_tools\observers\grade_update::observe_grade_updated',
    ],*/
];
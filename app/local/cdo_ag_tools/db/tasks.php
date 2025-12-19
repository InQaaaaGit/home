<?php

$tasks = [
    [
        'classname' => 'local_cdo_ag_tools\tasks\update_grade_for_double_coefficient',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    [
        'classname' => 'local_cdo_ag_tools\tasks\sender_notifications_task',
        'blocking' => 0,
        'minute' => '31',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    [
        'classname' => 'local_cdo_ag_tools\task\sync_1c_grades',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '2',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    [
        'classname' => 'local_cdo_ag_tools\tasks\weekly_quiz_report_task',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '10',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '0', // Воскресенье
    ],
];

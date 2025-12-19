<?php
$callbacks = [
    [
        'hook' => \core\hook\output\before_standard_head_html_generation::class,
        'callback' => [\local_videoprogress\hook\output\before_standard_head_html_generation::class, 'callback'],
        'priority' => 500,
    ],
]; 
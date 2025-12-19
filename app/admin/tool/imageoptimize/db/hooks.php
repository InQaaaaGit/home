<?php

use tool_imageoptimize\hooks\optimizer;

$callbacks = [
    [
        'hook' => core_files\hook\after_file_created::class,
        'callback' => [optimizer::class, 'optimize'],
        'priority' => 500,
    ],
];
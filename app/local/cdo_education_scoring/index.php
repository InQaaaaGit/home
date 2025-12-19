<?php

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

global $PAGE, $OUTPUT, $USER, $CFG;
require_login();

$plugin = 'local_cdo_education_scoring';
$title = get_string('pluginname', $plugin);
$url = new moodle_url('/local/cdo_education_scoring/index.php');
$discipline_id = optional_param("discipline", '', PARAM_TEXT);
$discipline_name = optional_param("discipline_name", '', PARAM_TEXT);
$use_only_scoring = optional_param('use_only_scoring', false, PARAM_BOOL);
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url($url);

// Определяем capabilities пользователя
$context = context_system::instance();
$capabilities = [
    'isAdmin' => has_capability('local/cdo_education_scoring:manage', $context) || is_siteadmin(),
    'isStudent' => has_capability('local/cdo_education_scoring:view', $context),
];
if ($use_only_scoring) {
    $capabilities = [
        'isAdmin' => 0,
        'isStudent' => true,
    ];
}

$amd_build = $CFG->cachejs ? $plugin."/prod-app-lazy" : $plugin."/dev-app-lazy";

$PAGE->requires->js_call_amd(
    $amd_build,
    'init',
    [
        [
            'user_id' => $USER->id,
            'capabilities' => $capabilities,
            'discipline_id' => $discipline_id,
            'discipline_name' => $discipline_name,
        ]
    ]
);

echo $OUTPUT->header();

echo <<<'EOT'
<div id="cdo_education_scoring">
</div>
EOT;

echo $OUTPUT->footer();

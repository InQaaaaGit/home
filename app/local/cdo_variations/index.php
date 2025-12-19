<?php

require('../../config.php');
require_login();
$plugin = 'local_cdo_variations';
$title = get_string('pluginname', $plugin);
$url = new moodle_url('/local/cdo_variations/index.php');

global $PAGE, $OUTPUT, $USER;
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->navbar->add(
    get_string('administrationsite'), '/admin/search.php'
);
$PAGE->navbar->add(
    $title, $url
);

$PAGE->requires->js_call_amd(
    "$plugin/app-lazy",
    'init',
    [
        [
            'user_id' => $USER->id,
            'excluded_mods' => explode(',', get_config($plugin, 'exclude_mods'))
        ]
    ]
);
echo $OUTPUT->header();
echo <<<'EOT'
<div id="local_cdo_variations">
 <main-app></main-app>
</div>
EOT;
echo $OUTPUT->footer();

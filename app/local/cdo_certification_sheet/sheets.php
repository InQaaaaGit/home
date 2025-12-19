<?php

require('../../config.php');
global $PAGE, $OUTPUT, $USER, $CFG;
require_login();
$plugin = 'local_cdo_certification_sheet';
$title = get_string('pluginname', 'local_cdo_certification_sheet');
$url = new moodle_url('/local/cdo_certification_sheet/sheets.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url($url);
$show_BRS = str_replace(' ', '', $CFG->show_BRS);
$division_for_BRS = str_replace(' ', '', $CFG->division_for_BRS);
$absence_guid = get_config('local_cdo_certification_sheet', 'guid_absence');
$show_download_button = (bool)get_config('local_cdo_certification_sheet', 'show_download_button');
$enable_vue_components = (bool)get_config('local_cdo_certification_sheet', 'enable_vue_components');
$amd_build = $CFG->cachejs ? $plugin."/prod-app-lazy" : $plugin."/dev-app-lazy";
$PAGE->requires->js_call_amd(
    $amd_build,
    'init',
    [
        [
            'user_id' => $USER->id,
            'show_BRS' => explode(",", $show_BRS),
            'division_for_BRS' => explode(",", $division_for_BRS),
            'absence_guid' => $absence_guid,
            'show_download_button' => $show_download_button,
            'enable_vue_components' => $enable_vue_components,
        ]
    ]
);

echo $OUTPUT->header();

echo <<<'EOT'
<div id="local_cdo_certification_sheet">
</div>
EOT;

echo $OUTPUT->footer();

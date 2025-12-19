<?php
defined('MOODLE_INTERNAL') || die();
$pluginname = 'local_cdo_variations';
$ADMIN->add('localplugins', new admin_externalpage(
    'local_cdo_variations_link',
    get_string('manage_variations', $pluginname),
    new moodle_url($CFG->wwwroot . '/local/cdo_variations/index.php')
));

$settings = new admin_settingpage($pluginname, get_string('title_settings_page', $pluginname));
$ADMIN->add('localplugins', $settings);

$settings->add(
    new admin_setting_configtext(
        $pluginname . '/exclude_mods',
        get_string('exclude_mods', $pluginname),
        get_string('exclude_mods_description', $pluginname),
        'forum',
        PARAM_TEXT
    )
);

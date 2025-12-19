<?php
defined('MOODLE_INTERNAL') || die;

$ADMIN->add(
    'localplugins',
    new admin_category(
        "local_cdo_vkr",
        get_string('pluginname', 'local_cdo_vkr')
    ),
);

$settings = new admin_settingpage(
    'local_cdo_vkr_settings_page',
    get_string('pluginname', 'local_cdo_vkr')
);

$settings->add(new admin_setting_configtext(
    "exclude_edu_level",
    get_string('exclude_edu_level', 'local_cdo_vkr'),
    get_string('exclude_edu_level_description', 'local_cdo_vkr'),
    ""
));

$settings->add(new admin_setting_configtext(
    "use_class_integration",
    get_string('use_class_integration', 'local_cdo_vkr'),
    get_string('use_class_integration_description', 'local_cdo_vkr'),
    "local_cdo_vkr\VKR\service_vkr_1c"
));


$ADMIN->add('localplugins', $settings);
<?php

defined('MOODLE_INTERNAL') || die();

$pluginname = 'local_cdo_certification_sheet';

if ($hassiteconfig) {
    $ADMIN->add(
        'localplugins',
        new admin_category(
            $pluginname,
            new lang_string('pluginname', $pluginname)
        )
    );
    $settingspage = new admin_settingpage(
        $pluginname.'_settings',
        new lang_string('settings_page', $pluginname)
    );
    if ($ADMIN->fulltree) {

        $settingspage->add(
            new admin_setting_configtext(
                'local_cdo_certification_sheet/guid_absence',
                new lang_string('guid_absence', $pluginname),
                new lang_string('guid_absence_description', $pluginname),
                '',
                PARAM_TEXT
            )
        );
        $settingspage->add(
            new admin_setting_configtext(
                'show_BRS',
                new lang_string('show_BRS', $pluginname),
                new lang_string('show_BRS_description', $pluginname),
                'бакалавриата, специалитета, магистратуры, базового высшего и специализированного высшего образования',
                PARAM_TEXT
            )
        );

        $settingspage->add(
            new admin_setting_configtext(
                'division_for_BRS',
                new lang_string('division_for_BRS', $pluginname),
                new lang_string('division_for_BRS_description', $pluginname),
                '',
                PARAM_TEXT
            )
        );

        $settingspage->add(
            new admin_setting_configcheckbox(
                'local_cdo_certification_sheet/show_download_button',
                new lang_string('show_download_button', $pluginname),
                new lang_string('show_download_button_description', $pluginname),
                0
            )
        );

        $settingspage->add(
            new admin_setting_configcheckbox(
                'local_cdo_certification_sheet/enable_vue_components',
                new lang_string('enable_vue_components', $pluginname),
                new lang_string('enable_vue_components_description', $pluginname),
                1
            )
        );
    }

    $ADMIN->add('localplugins', $settingspage);
}

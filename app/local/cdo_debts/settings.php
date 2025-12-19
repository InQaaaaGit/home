<?php
defined('MOODLE_INTERNAL') || die;
if ($hassiteconfig) {
    $ADMIN->add(
        'localplugins',
        new admin_category(
            'local_cdo_debts',
            new lang_string('pluginname', 'local_cdo_debts')
        )
    );
    $settingspage = new admin_settingpage(
        'local_cdo_debts_settings',
        new lang_string('pluginname', 'local_cdo_debts')
    );
    if ($ADMIN->fulltree) {


        $settingspage->add(
            new admin_setting_configcheckbox(
                'local_cdo_show_library_debts',
                get_string('show_library_debts', 'local_cdo_debts'),
                get_string('show_library_debts_description', 'local_cdo_debts'),
                1,
                PARAM_BOOL
            )
        );

        $settingspage->add(
            new admin_setting_configcheckbox(
                'local_cdo_show_finance_debts',
                get_string('show_finance_debts', 'local_cdo_debts'),
                get_string('show_finance_debts_description', 'local_cdo_debts'),
                1,
                PARAM_BOOL
            )
        );
    }

    $ADMIN->add('localplugins', $settingspage);
}
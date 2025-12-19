<?php

defined('MOODLE_INTERNAL') || die();

$pluginname = 'local_cdo_certification_sheet';

if ($hassiteconfig) {
    // Создаем отдельную категорию "Плагины" в админке
    $ADMIN->add(
        'root',
        new admin_category(
            'plugins',
            get_string('plugins', 'admin')
        )
    );
    
    // Создаем подкатегорию для нашего плагина
    $ADMIN->add(
        'plugins',
        new admin_category(
            $pluginname,
            new lang_string('pluginname', $pluginname)
        )
    );
    
    // Основные настройки
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

    $ADMIN->add($pluginname, $settingspage);

    // Настройки компоновки
    $layoutpage = new admin_settingpage(
        $pluginname.'_layout',
        new lang_string('layout_settings', $pluginname)
    );
    
    if ($ADMIN->fulltree) {
        // Настройки компоновки ведомостей
        $layoutpage->add(new admin_setting_heading(
            $pluginname.'/layout_settings',
            new lang_string('layout_settings', $pluginname),
            new lang_string('layout_settings_desc', $pluginname)
        ));

        // Показывать левую панель
        $layoutpage->add(new admin_setting_configcheckbox(
            $pluginname.'/show_left_panel',
            new lang_string('show_left_panel', $pluginname),
            new lang_string('show_left_panel_desc', $pluginname),
            1
        ));

        // Показывать правую панель
        $layoutpage->add(new admin_setting_configcheckbox(
            $pluginname.'/show_right_panel',
            new lang_string('show_right_panel', $pluginname),
            new lang_string('show_right_panel_desc', $pluginname),
            1
        ));

        // Настройка выбора типа компоновки
        $layout_options = array(
            'default' => new lang_string('layout_type_default', $pluginname),
            'two-rows' => new lang_string('layout_type_two_rows', $pluginname),
            'vertical' => new lang_string('layout_type_vertical', $pluginname),
        );
        
        $layoutpage->add(new admin_setting_configselect(
            $pluginname.'/layout_type',
            new lang_string('layout_type', $pluginname),
            new lang_string('layout_type_desc', $pluginname),
            'default',
            $layout_options
        ));

        // Дополнительные настройки для будущих компонентов
        $layoutpage->add(new admin_setting_heading(
            $pluginname.'/advanced_layout',
            new lang_string('advanced_layout', $pluginname),
            new lang_string('advanced_layout_desc', $pluginname)
        ));

        // Включить кастомные компоненты
        $layoutpage->add(new admin_setting_configcheckbox(
            $pluginname.'/enable_custom_components',
            new lang_string('enable_custom_components', $pluginname),
            new lang_string('enable_custom_components_desc', $pluginname),
            0
        ));
    }
    
    $ADMIN->add($pluginname, $layoutpage);
}

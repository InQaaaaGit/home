<?php

use local_cdo_ag_tools\helpers\helper;

defined('MOODLE_INTERNAL') || die();

$pluginname = 'local_cdo_ag_tools';
if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_cdo_ag_tools_execute',
        get_string('clear_zero_grades_link', 'local_cdo_ag_tools'),
        new moodle_url($CFG->wwwroot . '/local/cdo_ag_tools/clearing.php')
    ));
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_cdo_ag_tools_init_doubling',
        get_string('run_regrade_course_link', 'local_cdo_ag_tools'),
        new moodle_url($CFG->wwwroot . '/local/cdo_ag_tools/form_update_grades_for_doubling.php')
    ));
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_cdo_ag_tools_availability',
        get_string('availability_settings_link', 'local_cdo_ag_tools'),
        new moodle_url($CFG->wwwroot . '/local/cdo_ag_tools/pages/settings_availability.php')
    ));
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_cdo_ag_tools_sync_1c',
        get_string('sync_all_grades_to_1c_link', 'local_cdo_ag_tools'),
        new moodle_url($CFG->wwwroot . '/local/cdo_ag_tools/sync_1c_all.php')
    ));
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_cdo_ag_tools_test_send_grade',
        get_string('test_send_grade_link', 'local_cdo_ag_tools'),
        new moodle_url($CFG->wwwroot . '/local/cdo_ag_tools/pages/test_send_grade.php')
    ));
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_cdo_ag_tools_grade_digest_example',
        get_string('grade_digest_example_link', 'local_cdo_ag_tools'),
        new moodle_url($CFG->wwwroot . '/local/cdo_ag_tools/grade_digest_example.php')
    ));
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_cdo_ag_tools_import_grades_json',
        get_string('import_grades_from_json_link', 'local_cdo_ag_tools'),
        new moodle_url($CFG->wwwroot . '/local/cdo_ag_tools/import_grades_from_json.php')
    ));
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_cdo_ag_tools_reset_overridden_grades',
        get_string('reset_overridden_grades', 'local_cdo_ag_tools'),
        new moodle_url($CFG->wwwroot . '/local/cdo_ag_tools/pages/reset_overridden_grades.php')
    ));
    $settings = new admin_settingpage(
        $pluginname,
        get_string('pluginname', $pluginname)
    );
    $ADMIN->add('localplugins', $settings);
    $settings->add(new admin_setting_heading(
        "$pluginname/heading",
        get_string('header', $pluginname),
        get_string('header_desc', $pluginname) ?? ''
    ));
    $fss = helper::get_file_system_instances();
    if (!empty($fss))
        $settings->add(new admin_setting_configselect(
            "$pluginname/file_repository",
            get_string('file_repository', $pluginname),
            get_string('file_repository_desc', $pluginname) ?? '',
            0,
            $fss
        ));

    $settings->add(new admin_setting_configtext(
        "$pluginname/qr_code_x",
        get_string('qr_code_x', $pluginname),
        get_string('qr_code_x_desc', $pluginname) ?? '',
        59
    ));
    $settings->add(new admin_setting_configtext(
        "$pluginname/qr_code_y",
        get_string('qr_code_y', $pluginname),
        get_string('qr_code_y_desc', $pluginname) ?? '',
        39
    ));
    $settings->add(new admin_setting_configtext(
        "$pluginname/qr_code_size",
        get_string('qr_code_size', $pluginname),
        get_string('qr_code_size_desc', $pluginname) ?? '',
        32
    ));
    $settings->add(new admin_setting_configtext(
        "$pluginname/fio_x",
        get_string('fio_x', $pluginname),
        get_string('fio_x_desc', $pluginname) ?? '',
        32
    ));
    $settings->add(new admin_setting_configtext(
        "$pluginname/fio_y",
        get_string('fio_y', $pluginname),
        get_string('fio_y_desc', $pluginname) ?? '',
        67
    ));

    $settings->add(new admin_setting_configcheckbox(
        "$pluginname/only_final_works",
        get_string('only_final_works', $pluginname),
        get_string('only_final_works_desc', $pluginname) ?? '',
        0
    ));

    // === Интеграция с 1С ===
    $settings->add(new admin_setting_heading(
        "$pluginname/onec_heading",
        'Интеграция с 1С',
        'Настройки для отправки оценок в систему 1С'
    ));

    $settings->add(new admin_setting_configcheckbox(
        "$pluginname/send_grades_to_1c",
        'Отправлять оценки в 1С',
        'Включить автоматическую отправку выставленных оценок в систему 1С',
        0
    ));

    $settings->add(new admin_setting_configselect(
        "$pluginname/grade_handling_strategy",
        'Стратегия обработки оценок',
        'Выберите как обрабатывать оценки для 1С',
        'database',
        [
            'database' => 'Сохранение в базу данных',
            'direct_send' => 'Прямая отправка в 1С',
            'combined' => 'Сохранение в БД + прямая отправка'
        ]
    ));

    $settings->add(new admin_setting_heading(
        "$pluginname/button_heading",
        '', // Empty heading, just a visual separator
        get_string('button_text', $pluginname) .
        ' <a href="' . $CFG->wwwroot . '/local/cdo_ag_tools/init_update_grades_for_doubling.php' . '">' .
        get_string('button_text', $pluginname) .
        '</a>'
    ));
}

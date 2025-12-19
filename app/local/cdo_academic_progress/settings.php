<?php
/**
 * Settings for local_cdo_academic_progress plugin.
 *
 * @package    local_cdo_academic_progress
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_cdo_academic_progress', get_string('pluginname', 'local_cdo_academic_progress'));
    
    // Настройка для отображения колонки "Анкеты"
    $settings->add(new admin_setting_configcheckbox(
        'local_cdo_academic_progress/show_surveys_column',
        get_string('show_surveys_column', 'local_cdo_academic_progress'),
        get_string('show_surveys_column_desc', 'local_cdo_academic_progress'),
        1
    ));

    // Настройка для отображения колонки с группами
    $settings->add(new admin_setting_configcheckbox(
        'local_cdo_academic_progress/show_groups_column',
        get_string('show_groups_column', 'local_cdo_academic_progress'),
        get_string('show_groups_column_desc', 'local_cdo_academic_progress'),
        1
    ));

    $ADMIN->add('localplugins', $settings);
}




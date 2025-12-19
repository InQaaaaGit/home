<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Settings for local_videoprogress plugin
 *
 * @package    local_videoprogress
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Создаем категорию для плагина
    $ADMIN->add('localplugins', new admin_category('local_videoprogress_category', 
        get_string('pluginname', 'local_videoprogress')));

    // Добавляем страницу настроек
    $settings = new admin_settingpage('local_videoprogress_settings', 
        get_string('settings', 'local_videoprogress'));

    if ($ADMIN->fulltree) {
        // Настройка интервала отслеживания
        $settings->add(new admin_setting_configtext(
            'local_videoprogress/trackinginterval',
            get_string('trackinginterval', 'local_videoprogress'),
            get_string('trackinginterval_desc', 'local_videoprogress'),
            5,
            PARAM_INT
        ));

        // Включение/отключение отслеживания
        $settings->add(new admin_setting_configcheckbox(
            'local_videoprogress/enabletracking',
            get_string('enabletracking', 'local_videoprogress'),
            get_string('enabletracking_desc', 'local_videoprogress'),
            1
        ));
    }

    $ADMIN->add('local_videoprogress_category', $settings);

    // Добавляем страницу отчета
    $ADMIN->add('local_videoprogress_category', new admin_externalpage(
        'videoprogressreport',
        get_string('videoprogressreport', 'local_videoprogress'),
        new moodle_url('/local/videoprogress/report.php'),
        'local/videoprogress:manage'
    ));
} 
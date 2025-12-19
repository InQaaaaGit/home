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
 * Plugin settings for local_cdo_education_scoring.
 *
 * @package     local_cdo_education_scoring
 * @category    admin
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/classes/admin/settings.php');

// Обработка очистки таблиц
local_cdo_education_scoring_handle_clear_tables();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_cdo_education_scoring_settings',
        new lang_string('pluginname', 'local_cdo_education_scoring'));

    $ADMIN->add('localplugins', $settings);

    // Добавляем ссылку на страницу для админов
    $adminPageUrl = new moodle_url('/local/cdo_education_scoring/index.php');
    $settings->add(new admin_setting_description(
        'local_cdo_education_scoring_admin_link',
        get_string('admin_page', 'local_cdo_education_scoring'),
        html_writer::div(
            html_writer::tag('p', get_string('admin_page_description', 'local_cdo_education_scoring')) .
            html_writer::tag('p',
                html_writer::link(
                    $adminPageUrl,
                    get_string('open_admin_page', 'local_cdo_education_scoring'),
                    [
                        'class' => 'btn btn-primary',
                        'target' => '_blank'
                    ]
                )
            ),
            'form-item'
        )
    ));

    // Добавляем кнопку для очистки таблиц
    $url = new moodle_url('/admin/settings.php', [
        'section' => 'local_cdo_education_scoring_settings',
        'confirm' => 1,
        'sesskey' => sesskey(),
    ]);

    $settings->add(new admin_setting_description(
        'local_cdo_education_scoring_clear_tables',
        get_string('clear_tables', 'local_cdo_education_scoring'),
        html_writer::div(
            html_writer::tag('p', get_string('clear_tables_description', 'local_cdo_education_scoring')) .
            html_writer::tag('p',
                html_writer::link(
                    $url,
                    get_string('clear_tables_button', 'local_cdo_education_scoring'),
                    [
                        'class' => 'btn btn-danger',
                        'onclick' => "return confirm('" . 
                            addslashes(get_string('clear_tables_confirm', 'local_cdo_education_scoring')) . 
                            "');"
                    ]
                )
            ),
            'form-item'
        )
    ));
}


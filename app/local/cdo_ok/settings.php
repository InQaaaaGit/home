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
 * Настройки плагина local_cdo_ok.
 *
 * @package    local_cdo_ok
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Создаем категорию настроек для плагина
    $ADMIN->add('localplugins', new admin_category('local_cdo_ok', get_string('pluginname', 'local_cdo_ok')));

    // Добавляем внешнюю ссылку на страницу администрирования анкет
    $ADMIN->add(
        'local_cdo_ok',
        new admin_externalpage(
            'local_cdo_ok_manage',
            get_string('manage_surveys', 'local_cdo_ok'),
            new moodle_url('/local/cdo_ok/index_new.php'),
            'moodle/site:config'
        )
    );

    // Добавляем внешнюю ссылку на страницу очистки ответов
    $ADMIN->add(
        'local_cdo_ok',
        new admin_externalpage(
            'local_cdo_ok_clear_responses',
            get_string('clear_responses', 'local_cdo_ok'),
            new moodle_url('/local/cdo_ok/clear_responses.php'),
            'moodle/site:config'
        )
    );
}


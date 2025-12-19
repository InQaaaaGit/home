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
 * Plugin administration pages are defined here.
 *
 * @package     mod_webinarru
 * @category    admin
 * @copyright   2023 Maks Grishin (CDO Global@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $PAGE;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_heading('mod_webinarru/general', get_string('settings/general','mod_webinarru'), get_string('settings/general_desc','mod_webinarru')));
    $settings->add(new admin_setting_configtext('mod_webinarru/accounts', get_string('settings/accounts','mod_webinarru'), get_string('settings/accounts_desc','mod_webinarru'), '', PARAM_RAW));
    $settings->add(new admin_setting_description('mod_webinarru/example', get_string('settings/example','mod_webinarru'), get_string('settings/example_desc','mod_webinarru')));
    $settings->add(new admin_setting_heading('mod_webinarru/api', get_string('settings/api','mod_webinarru'), get_string('settings/api_desc','mod_webinarru')));
    $settings->add(new admin_setting_configtext('mod_webinarru/userapi_url', get_string('settings/userapi_url','mod_webinarru'), get_string('settings/userapi_url_desc','mod_webinarru'), 'https://userapi.mts-link.ru', PARAM_URL));
    $settings->add(new admin_setting_configtext('mod_webinarru/events_url', get_string('settings/events_url','mod_webinarru'), get_string('settings/events_url_desc','mod_webinarru'), 'https://events.mts-link.ru', PARAM_URL));
    $settings->add(new admin_setting_heading('mod_webinarru/functions', get_string('settings/functions','mod_webinarru'), get_string('settings/functions_desc','mod_webinarru')));
    $settings->add(new admin_setting_configcheckbox('mod_webinarru/free_access', get_string('settings/free_access','mod_webinarru'), get_string('settings/free_access_desc','mod_webinarru'), 0));
//  $settings->add(new admin_setting_configcheckbox('mod_webinarru/auto_start', get_string('settings/auto_start','mod_webinarru'), get_string('settings/auto_start_desc','mod_webinarru'), 0));
    $settings->add(new admin_setting_heading('mod_webinarru/form', get_string('settings/form','mod_webinarru'), get_string('settings/form_desc','mod_webinarru')));
    $settings->add(new admin_setting_configcheckbox('mod_webinarru/show_calendar', get_string('settings/show_calendar','mod_webinarru'), get_string('settings/show_calendar_desc','mod_webinarru'), 0));
    $settings->add(new admin_setting_configcheckbox('mod_webinarru/show_selected_date', get_string('settings/show_selected_date','mod_webinarru'), get_string('settings/show_selected_date_desc','mod_webinarru'), 1));
    $settings->add(new admin_setting_configcheckbox('mod_webinarru/change_submit_buttons', get_string('settings/change_submit_buttons','mod_webinarru'), get_string('settings/change_submit_buttons_desc','mod_webinarru'), 0));
    $settings->add(new admin_setting_configcheckbox('mod_webinarru/disable_tags', get_string('settings/disable_tags','mod_webinarru'), get_string('settings/disable_tags_desc','mod_webinarru'), 0));
    $settings->add(new admin_setting_heading('mod_webinarru/help', get_string('settings/help','mod_webinarru'), get_string('settings/help_desc','mod_webinarru')));
    $settings->add(new admin_setting_configcheckbox('mod_webinarru/show_help', get_string('settings/show_help','mod_webinarru'), get_string('settings/show_help_desc','mod_webinarru'), 0));
    $settings->add(new admin_setting_configtext('mod_webinarru/url_help', get_string('settings/url_help','mod_webinarru'), get_string('settings/url_help_desc','mod_webinarru'), '#', PARAM_RAW));

    // Подключить модуль YUI для удаления пробелов из текстового поля mod_webinarru/accounts
    $PAGE->requires->yui_module('moodle-mod_webinarru-remove_spaces', 'M.mod_webinarru.remove_spaces.init');
}

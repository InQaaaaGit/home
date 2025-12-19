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
 * Plugin settings
 *
 * @package   local_cdo_order_documents
 * @copyright 2024 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_cdo_order_documents', get_string('pluginname', 'local_cdo_order_documents'));
    $ADMIN->add('localplugins', $settings);

    $name = 'local_cdo_order_documents/show_document_type_form';
    $title = get_string('setting:show_document_type_form', 'local_cdo_order_documents');
    $description = get_string('setting:show_document_type_form_desc', 'local_cdo_order_documents');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $settings->add($setting);

    $name = 'local_cdo_order_documents/show_certificates_table';
    $title = get_string('setting:show_certificates_table', 'local_cdo_order_documents');
    $description = get_string('setting:show_certificates_table_desc', 'local_cdo_order_documents');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $settings->add($setting);

    $name = 'local_cdo_order_documents/show_download_column';
    $title = get_string('setting:show_download_column', 'local_cdo_order_documents');
    $description = get_string('setting:show_download_column_desc', 'local_cdo_order_documents');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $settings->add($setting);
}

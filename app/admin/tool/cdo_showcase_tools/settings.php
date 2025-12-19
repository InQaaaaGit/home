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
 * Plugin settings and administration.
 *
 * @package     tool_cdo_showcase_tools
 * @copyright   2024 Your Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Create a new admin category for the plugin.
    $ADMIN->add(
        'tools',
        new admin_externalpage(
            'tool_cdo_showcase_tools',
            get_string('pluginname', 'tool_cdo_showcase_tools'),
            new moodle_url('/admin/tool/cdo_showcase_tools/index.php'),
            'tool/cdo_showcase_tools:view'
        )
    );

    // No separate settings page needed for basic functionality.
    // If you need configuration options, create them like this:
    /*
    $settings = new admin_settingpage('tool_cdo_showcase_tools_settings', 
        get_string('settings', 'tool_cdo_showcase_tools'));
    
    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_configtext(
            'tool_cdo_showcase_tools/example_setting',
            get_string('example_setting', 'tool_cdo_showcase_tools'),
            get_string('example_setting_desc', 'tool_cdo_showcase_tools'),
            'default_value',
            PARAM_TEXT
        ));
    }
    
    $ADMIN->add('tools', $settings);
    */
} 
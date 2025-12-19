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
 * Global settings for block_cdo_notification
 *
 * @package    block_cdo_notification
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if ($hassiteconfig) {
    $settings->add(new admin_setting_configtext(
        'block_cdo_notification/maxnotifications',
        get_string('maxnotifications', 'block_cdo_notification'),
        get_string('maxnotifications_desc', 'block_cdo_notification'),
        3,
        PARAM_INT
    ));
    $settings->add(new admin_setting_configtext(
        'block_cdo_notification/perpage',
        get_string('perpage', 'block_cdo_notification'),
        get_string('perpage_desc', 'block_cdo_notification'),
        10,
        PARAM_INT
    ));
} 
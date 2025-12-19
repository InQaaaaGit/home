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
 * Library functions for the CDO Showcase Tools plugin.
 *
 * @package     tool_cdo_showcase_tools
 * @copyright   2024 Your Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Serves the plugin's files.
 *
 * @param stdClass $course The course object
 * @param stdClass $cm The course module object
 * @param context $context The context
 * @param string $filearea The name of the file area
 * @param array $args Extra arguments (itemid, path)
 * @param bool $forcedownload Whether or not to force download
 * @param array $options Additional options affecting the file serving
 * @return bool False if the file not found, just send the file otherwise and do not return anything
 */
function tool_cdo_showcase_tools_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    // Check capability.
    if (!has_capability('tool/cdo_showcase_tools:view', $context)) {
        return false;
    }

    // Extract arguments.
    $itemid = array_shift($args);
    $filename = array_pop($args);
    
    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    // Retrieve the file from the file area.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'tool_cdo_showcase_tools', $filearea, $itemid, $filepath, $filename);
    
    if (!$file) {
        return false;
    }

    send_stored_file($file, null, 0, $forcedownload, $options);
} 
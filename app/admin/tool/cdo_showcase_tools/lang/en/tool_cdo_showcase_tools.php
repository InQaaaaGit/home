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
 * Language strings for the CDO Showcase Tools plugin.
 *
 * @package     tool_cdo_showcase_tools
 * @copyright   2024 Your Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'CDO Showcase Tools';
$string['privacy:metadata'] = 'The CDO Showcase Tools plugin does not store any personal data.';
$string['cdo_showcase_tools:view'] = 'View CDO Showcase Tools';
$string['cdo_showcase_tools:manage'] = 'Manage CDO Showcase Tools';
$string['heading'] = 'CDO Showcase Tools';
$string['description'] = 'Tool for demonstrating CDO functionality and features.';
$string['welcome'] = 'Welcome to CDO Showcase Tools';
$string['nopermission'] = 'You do not have permission to access this page.';

// Tool-specific strings.
$string['demo_tool'] = 'Demo Tool';
$string['demo_tool_desc'] = 'A demonstration tool for testing functionality';
$string['toolnotfound'] = 'Tool "{$a}" not found';
$string['tooldisabled'] = 'Tool "{$a}" is disabled';
$string['availabletools'] = 'Available Tools';
$string['toolexecution'] = 'Tool Execution';
$string['executionfailed'] = 'Tool execution failed';
$string['executionsuccess'] = 'Tool executed successfully';

// External API strings.
$string['coursenotfound'] = 'Course with ID {$a} not found';
$string['nopermissiontoviewgrades'] = 'You do not have permission to view grades for this course';
$string['errorprocessingdata'] = 'Error processing grade data: {$a}';
$string['lettergradeservice'] = 'Letter Grade Service';
$string['lettergradeservice_desc'] = 'Web service for retrieving course letter grades';
$string['gradesdataprocessed'] = 'Grade scales data processed successfully';
$string['nogradescalesfound'] = 'No grade scales found for the specified criteria';
$string['gradescale'] = 'Grade Scale';
$string['gradescales'] = 'Grade Scales';
$string['minimum'] = 'Minimum';
$string['maximum'] = 'Maximum';
$string['gradename'] = 'Grade Name';
$string['lettercode'] = 'Letter Code';
$string['gradevalue'] = 'Grade Value'; 
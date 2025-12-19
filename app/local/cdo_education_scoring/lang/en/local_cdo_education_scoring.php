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
 * Language strings for local_cdo_education_scoring plugin.
 *
 * @package     local_cdo_education_scoring
 * @category    string
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'CDO Education Scoring';
$string['privacy:metadata'] = 'The CDO Education Scoring plugin does not store any personal data.';

// Capabilities.
$string['cdo_education_scoring:manage'] = 'Manage quality surveys';
$string['cdo_education_scoring:view'] = 'View quality surveys';
$string['cdo_education_scoring:viewstats'] = 'View survey statistics';

// Errors.
$string['invalidjson'] = 'Invalid JSON data';
$string['invalidaction'] = 'Invalid action: {$a}';

// Student interface.
$string['no_active_surveys'] = 'There are no active surveys available at the moment.';
$string['complete_survey'] = 'Complete survey';
$string['view_survey'] = 'View';
$string['survey_available'] = 'Available';
$string['survey_completed'] = 'Completed';
$string['due_date'] = 'Due date';

// Plugin settings.
$string['clear_tables'] = 'Clear plugin data';
$string['clear_tables_description'] = 'This operation will delete all data from plugin tables (surveys, questions, responses). This action is irreversible!';
$string['clear_tables_button'] = 'Clear all tables';
$string['clear_tables_confirm'] = 'Are you sure you want to clear all plugin tables? This action is irreversible and will delete all surveys, questions, and responses.';
$string['tables_cleared'] = 'All plugin tables have been successfully cleared';

// Admin page link.
$string['admin_page'] = 'Management page';
$string['admin_page_description'] = 'Go to the quality surveys management page';
$string['open_admin_page'] = 'Open management page';


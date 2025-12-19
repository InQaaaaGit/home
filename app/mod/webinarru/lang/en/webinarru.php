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
 * Plugin strings are defined here.
 *
 * @package     mod_webinarru
 * @category    string
 * @copyright   2023 Maks Grishin (CDO Global@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// General
$string['pluginname'] = 'mts-link.ru by CDO Global';
$string['modulename'] = 'Videoconference on mts-link.ru';
$string['modulenameplural'] = 'Videoconferences on mts-link.ru';

// Form for creating/updating a module instance
$string['mod_form/help_button'] = 'Read the instructions for this course element';
$string['mod_form/designed_by'] = 'Designed by CDO Global';
$string['mod_form/purpose'] = 'Videoconference purpose';
$string['mod_form/purpose_desc'] = 'Displayed in the title of the module in the course and on the mts-link.ru service<br><b>Do not neglect</b>';
$string['mod_form/webinar_date'] = 'Date of the videoconference';
$string['mod_form/webinar_date_desc'] = 'Checking the availability of dates for creating a videoconference depends on this setting<br><b>Be careful</b>';
$string['mod_form/webinar_duration'] = 'Duration';
$string['mod_form/webinar_duration_desc'] = 'Checking the availability of dates for creating a videoconference depends on this setting<br><b>Be careful</b></b>';
$string['mod_form/check_free_time'] = 'Check free time for the selected date';
$string['mod_form/show_selected_date'] = 'Display selected time range';
$string['mod_form/submitbutton1'] = 'Save and go to mts-link.ru';
$string['mod_form/submitbutton2'] = 'Save and return to course';

// Array with videoconference purpose values
$string['mod_form/purpose/credit'] = 'Credit';
$string['mod_form/purpose/exam'] = 'Exam';


//
$string['mod_form/type/webinar'] = 'Webinar';
$string['mod_form/type/meeting'] = 'Meeting';
$string['mod_form/type/training'] = 'Training';
$string['mod_form/type'] = 'Type';

// Array with videoconference duration values
$string['mod_form/webinar_duration/900'] = '15 minutes';
$string['mod_form/webinar_duration/1800'] = '30 minutes';
$string['mod_form/webinar_duration/2700'] = '45 minutes';
$string['mod_form/webinar_duration/3600'] = '1 hour';
$string['mod_form/webinar_duration/5400'] = '1 hour 30 minutes';
$string['mod_form/webinar_duration/7200'] = '2 hours';
$string['mod_form/webinar_duration/9000'] = '2 hours 30 minutes';
$string['mod_form/webinar_duration/10800'] = '3 hours';

// Module configuration form
$string['settings/general'] = 'Used mts-link.ru accounts';
$string['settings/general_desc'] = 'These options are used <b>always</b>';
$string['settings/accounts'] = 'Accounts data';
$string['settings/accounts_desc'] = 'Data format - JSON<br>The number of specified accounts is not limited';
$string['settings/example'] = 'Sample data';
$string['settings/example_desc'] = '<pre>{<br>  "0": {<br>    "login": "test_1@example.ru",<br>    "password": "password_1",<br>    "token": "token_1"<br>  },<br>  "1": {<br>    "login": "test_2@example.ru",<br>    "password": "password_2",<br>    "token": "token_2"<br>  },<br>  "2": {<br>    "login": "test_3@example.ru",<br>    "password": "password_3",<br>    "token": "token_3"<br>  }<br>}<pre>';
$string['settings/api'] = 'API Settings';
$string['settings/api_desc'] = 'Connection settings for video conferencing service API';
$string['settings/userapi_url'] = 'User API URL';
$string['settings/userapi_url_desc'] = 'Base URL for User API (e.g., https://userapi.mts-link.ru)';
$string['settings/events_url'] = 'Events API URL';
$string['settings/events_url_desc'] = 'Base URL for Events API (e.g., https://events.mts-link.ru)';
$string['settings/functions'] = 'Configuration for individual videoconferencing features';
$string['settings/functions_desc'] = 'These options are used <b>always</b>';
$string['settings/free_access'] = 'Free access';
$string['settings/free_access_desc'] = '<b>If checked</b> - anyone can join the video conference';
$string['settings/auto_start'] = 'Autostart';
$string['settings/auto_start_desc'] = '<b>If checked</b> - the video conference will start automatically at the specified time';
$string['settings/form'] = 'Changing the form for creating/updating a course module';
$string['settings/form_desc'] = 'These options affect the display of the create/update course module page';
$string['settings/show_calendar'] = 'Show calendar';
$string['settings/show_calendar_desc'] = '<b>If unchecked</b> - the calendar to the right of the date picker will be hidden';
$string['settings/show_selected_date'] = 'Show selected time range';
$string['settings/show_selected_date_desc'] = '<b>If not checked</b> - range will not be displayed by default (user can enable display)';
$string['settings/change_submit_buttons'] = 'Change course item save button names';
$string['settings/change_submit_buttons_desc'] = '<b>If not checked</b> - buttons will have default names';
$string['settings/disable_tags'] = 'Disable tag editing';
$string['settings/disable_tags_desc'] = '<b>If not checked</b> - the field for editing tags will be available';
$string['settings/help'] = 'Additionally';
$string['settings/help_desc'] = 'Extra options';
$string['settings/show_help'] = 'Display notification to go to instructions';
$string['settings/show_help_desc'] = '<b>If checked</b> - on the page for creating/editing a course element there will be a notification with a button to go to the instruction';
$string['settings/url_help'] = 'Link to instructions';
$string['settings/url_help_desc'] = 'This link will be taken when you click on the button';

// View page
$string['view/link'] = 'Go to the videoconference on mts-link.ru';

// Notifications
$string['notification/error_accounts'] = 'This course element cannot be created because the module is not configured:<br><b>There is no data about mts-link.ru accounts used or there are errors in JSON</b><br><br>Please contact the administrator!';
$string['notification/error_tokens'] = '<b>The date you specified is busy. Please select a different date.</b><br><br>This error is caused by the limited number of mts-link.ru accounts<br>as well as the limitation on the number of simultaneous video conferences on each account.';
$string['notification/error_create_event'] = '<b>Failed to create an event on mts-link.ru</b><br><br>This error could be caused by problems with the mts-link.ru service<br>Try creating the course element again.';
$string['notification/error_change_event'] = '<b>Failed to edit the event on mts-link.ru</b><br><br>This error could be caused by problems with the mts-link.ru service<br>Try creating the course element again.';

// AJAX
$string['ajax/desc_error_accounts'] = 'Unable to check selected range';
$string['ajax/desc_free_range'] = 'Selected time range is free';
$string['ajax/desc_busy_range'] = 'Selected time range is busy';
$string['ajax/error_accounts'] = 'Contact your administrator. The data on the mts-link.ru accounts used is invalid.';
$string['ajax/label_teacher'] = 'Teacher';
$string['ajax/label_start_of'] = 'Start';
$string['ajax/label_end_of'] = 'Ending';
$string['ajax/legend_busy'] = 'Busy time on each mts-link.ru account available within this module (Total: ';
$string['ajax/legend_selected'] = 'Selected time range';
$string['ajax/legend_selected_desc'] = 'Please note that the selected range is free if it does not overlap with busy ones for any of the available mts-link.ru accounts';

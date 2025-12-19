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
 * Language strings for the CDO Notification block
 *
 * @package    block_cdo_notification
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'CDO Notification';
$string['cdo_notification:addinstance'] = 'Add a new CDO Notification block';
$string['cdo_notification:myaddinstance'] = 'Add a new CDO Notification block to Dashboard';
$string['cdo_notification:view'] = 'View CDO Notification block';
$string['cdo_notification:manage'] = 'Manage CDO Notification block';
$string['cdo_notification:edit'] = 'Edit CDO Notification block';
$string['cdo_notification:delete'] = 'Delete CDO Notification block';

// Notification headers template strings
$string['notifications'] = 'Notifications';
$string['viewdetails'] = 'View details';
$string['confirmdeletenotification'] = 'Are you sure you want to delete this notification?';
$string['viewallnotifications'] = 'View all notifications';
$string['markasread'] = 'Mark as read';
$string['markasunread'] = 'Mark as unread';
$string['deletenotification'] = 'Delete notification';
$string['notificationdeleted'] = 'Notification deleted';
$string['notificationmarkedasread'] = 'Notification marked as read';
$string['errorloadingnotifications'] = 'Error loading notifications';
$string['noaccess'] = 'No access to notifications';
$string['nonotifications'] = 'No active notifications.';

// Notification detail page strings
$string['notificationdetails'] = 'Notification Details';
$string['notificationnotfound'] = 'Notification not found';
$string['backtolist'] = 'Back to list';
$string['notificationid'] = 'Notification ID';
$string['notificationdate'] = 'Notification date';
$string['notificationheader'] = 'Notification header';
$string['notificationbody'] = 'Notification body';

$string['maxnotifications'] = 'Maximum number of notifications';
$string['maxnotifications_desc'] = 'How many latest notifications to show in the block (sorted by date, descending).';

// Notifications list page strings
$string['notificationslist'] = 'Notifications List';
$string['totalnotifications'] = 'Total notifications: {$a}';
$string['addnotification'] = 'Add notification';
$string['searchnotifications'] = 'Search notifications';
$string['searchplaceholder'] = 'Enter text to search...';
$string['sortby'] = 'Sort by';
$string['sortbydate'] = 'Date';
$string['sortbytitle'] = 'Title';
$string['order'] = 'Order';
$string['newestfirst'] = 'Newest first';
$string['oldestfirst'] = 'Oldest first';
$string['nosearchresults'] = 'No results found for your search';
$string['nonotificationsdesc'] = 'There are no active notifications at the moment';

// Edit notification strings
$string['editnotification'] = 'Edit notification';
$string['headerhelp'] = 'Short notification header (maximum 255 characters)';
$string['bodyhelp'] = 'Full notification text. HTML markup is supported';
$string['isactive'] = 'Active notification';
$string['activehelp'] = 'Active notifications are displayed to users';

// Main page strings
$string['recentnotifications'] = 'Recent notifications';
$string['perpage'] = 'Notifications per page';
$string['perpage_desc'] = 'How many notifications to show per page when viewing all notifications.';

$string['eventnotificationviewed'] = 'Notification viewed'; 
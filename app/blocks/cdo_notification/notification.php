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
 * Notification detail page
 *
 * @package    block_cdo_notification
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

use block_cdo_notification\notification_manager;

// Get notification ID from URL parameter
$notificationid = required_param('id', PARAM_ALPHANUMEXT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

// Set up page
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/cdo_notification/notification.php', ['id' => $notificationid]);
$PAGE->set_title(get_string('notificationdetails', 'block_cdo_notification'));
$PAGE->set_heading(get_string('notificationdetails', 'block_cdo_notification'));
$PAGE->set_pagelayout('standard');

// Check capabilities
//require_capability('block/cdo_notification:view', context_system::instance());

// Add breadcrumb navigation
$PAGE->navbar->add(get_string('pluginname', 'block_cdo_notification'), new moodle_url('/blocks/cdo_notification/'));
$PAGE->navbar->add(get_string('notificationdetails', 'block_cdo_notification'));

// Get notification data
$manager = new notification_manager();
$notification = null;
$error = null;

try {
    $notifications = $manager->get_concrete_notifications($notificationid);
    
    // Find the specific notification by ID
    foreach ($notifications as $notif) {
        if ($notif['id'] === $notificationid) {
            $notification = $notif;
            break;
        }
    }
    
    if (!$notification) {
        throw new moodle_exception('notificationnotfound', 'block_cdo_notification');
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Handle form actions
$action = optional_param('action', '', PARAM_ALPHA);
if ($action && $notification) {
    require_sesskey();
    
    switch ($action) {
        case 'delete':
            // Handle delete action
            if (has_capability('block/cdo_notification:delete', context_system::instance())) {
                // Here you would implement the actual deletion logic
                // For now, we'll just redirect back
                redirect(new moodle_url('/blocks/cdo_notification/', ['deleted' => 1]));
            }
            break;
            
        case 'markasread':
            // Handle mark as read action
            if (has_capability('block/cdo_notification:edit', context_system::instance())) {
                // Here you would implement the mark as read logic
                // For now, we'll just redirect back
                redirect($PAGE->url, get_string('notificationmarkedasread', 'block_cdo_notification'));
            }
            break;
    }
}

// Start output
if ($error) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification($error, 'error');
    echo $OUTPUT->continue_button($returnurl ?: new moodle_url('/blocks/cdo_notification/'));
    echo $OUTPUT->footer();
    exit;
}

if (!$notification) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('notificationnotfound', 'block_cdo_notification'), 'error');
    echo $OUTPUT->continue_button($returnurl ?: new moodle_url('/blocks/cdo_notification/'));
    echo $OUTPUT->footer();
    exit;
}

// Логирование события просмотра уведомления
\block_cdo_notification\event\notification_viewed::create([
    'objectid' => $notification['id'],
    'context' => context_system::instance(),
    'userid' => $USER->id,
])->trigger();

// Форматируем дату и body_message для шаблона
$notification['date'] = (new DateTime($notification['date']))->format('d.m.Y H:i:s');
$notification['body_message'] = format_text($notification['body_message'], FORMAT_HTML);
$notification['backurl'] = $returnurl ?: (new moodle_url('/blocks/cdo_notification/index.php'))->out();

require_once($CFG->dirroot . '/blocks/cdo_notification/classes/output/notifications/notification_detail_renderable.php');
$renderable = new \block_cdo_notification\output\notifications\notification_detail_renderable($notification);
$renderer = $PAGE->get_renderer('block_cdo_notification', 'notifications');
echo $OUTPUT->header();
echo $renderer->render_from_template('block_cdo_notification/notification_detail', $renderable->export_for_template($renderer));
echo $OUTPUT->footer();

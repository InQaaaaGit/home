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
 * Delete notification page
 *
 * @package    block_cdo_notification
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use block_cdo_notification\notification_manager;

// Get parameters
$notificationid = required_param('id', PARAM_ALPHANUMEXT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

// Set up page
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/cdo_notification/delete.php', ['id' => $notificationid]);
$PAGE->set_title(get_string('deletenotification', 'block_cdo_notification'));
$PAGE->set_heading(get_string('deletenotification', 'block_cdo_notification'));
$PAGE->set_pagelayout('standard');

// Check capabilities
require_capability('block/cdo_notification:delete', context_system::instance());

// Check sesskey
require_sesskey();

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

// Start output
echo $OUTPUT->header();

if ($error) {
    echo $OUTPUT->notification($error, 'error');
    echo $OUTPUT->continue_button($returnurl ?: new moodle_url('/blocks/cdo_notification/notifications_list.php'));
    echo $OUTPUT->footer();
    exit;
}

if (!$notification) {
    echo $OUTPUT->notification(get_string('notificationnotfound', 'block_cdo_notification'), 'error');
    echo $OUTPUT->continue_button($returnurl ?: new moodle_url('/blocks/cdo_notification/notifications_list.php'));
    echo $OUTPUT->footer();
    exit;
}

// Display confirmation form
?>

<div class="delete-notification-container">
    <div class="delete-notification-header">
        <h2><?php echo get_string('deletenotification', 'block_cdo_notification'); ?></h2>
    </div>

    <div class="delete-notification-content">
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            <strong><?php echo get_string('warning', 'core'); ?></strong>
            <p><?php echo get_string('confirmdeletenotification', 'block_cdo_notification'); ?></p>
        </div>

        <div class="notification-preview">
            <h3><?php echo format_string($notification['header']); ?></h3>
            <div class="notification-meta">
                <span class="notification-date">
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                    <?php echo format_string($notification['date']); ?>
                </span>
                <span class="notification-id">
                    <i class="fa fa-hashtag" aria-hidden="true"></i>
                    <?php echo format_string($notification['notification-idx']); ?>
                </span>
            </div>
            <div class="notification-body">
                <?php echo format_text($notification['body_message'], FORMAT_HTML); ?>
            </div>
        </div>

        <div class="delete-notification-actions">
            <form method="post" action="<?php echo $PAGE->url; ?>">
                <input type="hidden" name="id" value="<?php echo $notificationid; ?>">
                <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
                <input type="hidden" name="confirm" value="1">
                
                <button type="submit" class="btn btn-danger">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                    <?php echo get_string('deletenotification', 'block_cdo_notification'); ?>
                </button>
                
                <a href="<?php echo $returnurl ?: new moodle_url('/blocks/cdo_notification/notifications_list.php'); ?>" 
                   class="btn btn-secondary">
                    <i class="fa fa-times" aria-hidden="true"></i>
                    <?php echo get_string('cancel', 'core'); ?>
                </a>
            </form>
        </div>
    </div>
</div>

<style>
.delete-notification-container {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.delete-notification-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    padding: 1.5rem;
    text-align: center;
}

.delete-notification-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.delete-notification-content {
    padding: 2rem;
}

.delete-notification-content .alert {
    margin-bottom: 2rem;
    border-radius: 6px;
}

.delete-notification-content .alert i {
    margin-right: 0.5rem;
}

.notification-preview {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.notification-preview h3 {
    margin: 0 0 1rem 0;
    color: #2c3e50;
    font-size: 1.25rem;
    font-weight: 600;
}

.notification-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #6c757d;
}

.notification-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.notification-body {
    color: #495057;
    line-height: 1.6;
}

.delete-notification-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.delete-notification-actions .btn {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.delete-notification-actions .btn-danger {
    background: #dc3545;
    border-color: #dc3545;
}

.delete-notification-actions .btn-danger:hover {
    background: #c82333;
    border-color: #bd2130;
}

/* Responsive design */
@media (max-width: 768px) {
    .delete-notification-content {
        padding: 1.5rem;
    }
    
    .notification-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .delete-notification-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .delete-notification-actions .btn {
        width: 100%;
    }
}
</style>

<?php
echo $OUTPUT->footer();
?> 
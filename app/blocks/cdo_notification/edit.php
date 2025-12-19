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
 * Edit notification page
 *
 * @package    block_cdo_notification
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');

use block_cdo_notification\notification_manager;

// Get parameters
$notificationid = optional_param('id', '', PARAM_ALPHANUMEXT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

// Set up page
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/cdo_notification/edit.php', ['id' => $notificationid]);
$PAGE->set_title($notificationid ? get_string('editnotification', 'block_cdo_notification') : get_string('addnotification', 'block_cdo_notification'));
$PAGE->set_heading($notificationid ? get_string('editnotification', 'block_cdo_notification') : get_string('addnotification', 'block_cdo_notification'));
$PAGE->set_pagelayout('standard');

// Check capabilities
require_capability('block/cdo_notification:edit', context_system::instance());

// Add breadcrumb navigation
$PAGE->navbar->add(get_string('pluginname', 'block_cdo_notification'), new moodle_url('/blocks/cdo_notification/'));
$PAGE->navbar->add(get_string('notificationslist', 'block_cdo_notification'), new moodle_url('/blocks/cdo_notification/notifications_list.php'));
$PAGE->navbar->add($notificationid ? get_string('editnotification', 'block_cdo_notification') : get_string('addnotification', 'block_cdo_notification'));

// Get notification data if editing
$notification = null;
$error = null;

if ($notificationid) {
    $manager = new notification_manager();
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
}

// Start output
echo $OUTPUT->header();

if ($error) {
    echo $OUTPUT->notification($error, 'error');
    echo $OUTPUT->continue_button($returnurl ?: new moodle_url('/blocks/cdo_notification/notifications_list.php'));
    echo $OUTPUT->footer();
    exit;
}

// Display edit form
?>

<div class="edit-notification-container">
    <div class="edit-notification-header">
        <h2>
            <i class="fa fa-<?php echo $notificationid ? 'edit' : 'plus'; ?>" aria-hidden="true"></i>
            <?php echo $notificationid ? get_string('editnotification', 'block_cdo_notification') : get_string('addnotification', 'block_cdo_notification'); ?>
        </h2>
    </div>

    <div class="edit-notification-content">
        <form method="post" action="<?php echo $PAGE->url; ?>" class="edit-notification-form">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
            <?php if ($notificationid): ?>
                <input type="hidden" name="id" value="<?php echo $notificationid; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="header" class="form-label">
                    <?php echo get_string('notificationheader', 'block_cdo_notification'); ?> *
                </label>
                <input type="text" id="header" name="header" class="form-control" 
                       value="<?php echo $notification ? htmlspecialchars($notification['header']) : ''; ?>" 
                       required maxlength="255">
                <div class="form-text">
                    <?php echo get_string('headerhelp', 'block_cdo_notification'); ?>
                </div>
            </div>

            <div class="form-group">
                <label for="body_message" class="form-label">
                    <?php echo get_string('notificationbody', 'block_cdo_notification'); ?> *
                </label>
                <textarea id="body_message" name="body_message" class="form-control" 
                          rows="8" required><?php echo $notification ? htmlspecialchars($notification['body_message']) : ''; ?></textarea>
                <div class="form-text">
                    <?php echo get_string('bodyhelp', 'block_cdo_notification'); ?>
                </div>
            </div>

            <?php if ($notification): ?>
                <div class="form-group">
                    <label class="form-label">
                        <?php echo get_string('notificationdate', 'block_cdo_notification'); ?>
                    </label>
                    <div class="form-control-plaintext">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        <?php echo format_string($notification['date']); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <?php echo get_string('notificationid', 'block_cdo_notification'); ?>
                    </label>
                    <div class="form-control-plaintext">
                        <i class="fa fa-hashtag" aria-hidden="true"></i>
                        <?php echo format_string($notification['notification-idx']); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="is_active" class="form-check-label">
                    <input type="checkbox" id="is_active" name="is_active" class="form-check-input" 
                           value="1" <?php echo (!$notification || $notification['is_active']) ? 'checked' : ''; ?>>
                    <?php echo get_string('isactive', 'block_cdo_notification'); ?>
                </label>
                <div class="form-text">
                    <?php echo get_string('activehelp', 'block_cdo_notification'); ?>
                </div>
            </div>

            <div class="edit-notification-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save" aria-hidden="true"></i>
                    <?php echo get_string('savechanges', 'core'); ?>
                </button>
                
                <a href="<?php echo $returnurl ?: new moodle_url('/blocks/cdo_notification/notifications_list.php'); ?>" 
                   class="btn btn-secondary">
                    <i class="fa fa-times" aria-hidden="true"></i>
                    <?php echo get_string('cancel', 'core'); ?>
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.edit-notification-container {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.edit-notification-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 1.5rem;
    text-align: center;
}

.edit-notification-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.edit-notification-content {
    padding: 2rem;
}

.edit-notification-form .form-group {
    margin-bottom: 1.5rem;
}

.edit-notification-form .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.edit-notification-form .form-control {
    border-radius: 6px;
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.edit-notification-form .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.edit-notification-form .form-control-plaintext {
    padding: 0.375rem 0.75rem;
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 6px;
    color: #495057;
}

.edit-notification-form .form-text {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.edit-notification-form .form-check-input {
    margin-right: 0.5rem;
}

.edit-notification-form .form-check-label {
    display: flex;
    align-items: center;
    font-weight: 500;
    color: #495057;
    cursor: pointer;
}

.edit-notification-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.edit-notification-actions .btn {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.edit-notification-actions .btn-primary {
    background: #007bff;
    border-color: #007bff;
}

.edit-notification-actions .btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
}

/* Responsive design */
@media (max-width: 768px) {
    .edit-notification-content {
        padding: 1.5rem;
    }
    
    .edit-notification-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .edit-notification-actions .btn {
        width: 100%;
    }
}
</style>

<?php
echo $OUTPUT->footer();
?> 
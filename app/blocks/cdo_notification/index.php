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
 * CDO Notification block main page
 *
 * @package    block_cdo_notification
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use block_cdo_notification\notification_manager;
use block_cdo_notification\output\notifications\main_renderable;

// Set up page
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/cdo_notification/');
$PAGE->set_title(get_string('pluginname', 'block_cdo_notification'));
$PAGE->set_heading(get_string('pluginname', 'block_cdo_notification'));
$PAGE->set_pagelayout('standard');

// Check capabilities
require_capability('block/cdo_notification:view', context_system::instance());

// Add breadcrumb navigation
$PAGE->navbar->add(get_string('pluginname', 'block_cdo_notification'));

// Параметры пагинации
$page = optional_param('page', 0, PARAM_INT);
$perpage = 5;
$offset = $page * $perpage;

// Get notifications data
$manager = new notification_manager();
$notifications = [];
$error = null;
$totalcount = 0;

try {
    $allnotifications = $manager->get_active_notifications();
    $totalcount = count($allnotifications);
    
    // Сортировка по дате (по убыванию)
    usort($allnotifications, function($a, $b) {
        return strtotime($b['date']) <=> strtotime($a['date']);
    });
    
    // Пагинация
    $shownotifications = array_slice($allnotifications, $offset, $perpage);
    
    // Process notifications for display
    foreach ($shownotifications as &$notification) {
        $notification['notification-idx'] = $notification['id'];
        $dateTime = new DateTime($notification['date']);
        $notification['date'] = $dateTime->format('d.m.Y H:i:s');
        $notification['short_body'] = strip_tags($notification['body_message']);
        if (strlen($notification['short_body']) > 100) {
            $notification['short_body'] = substr($notification['short_body'], 0, 100) . '...';
        }
    }
    
    $notifications = $shownotifications;
    
} catch (Exception $e) {
    $error = $e->getMessage();
}

echo $OUTPUT->header();

if ($error) {
    echo $OUTPUT->notification($error, 'error');
    echo $OUTPUT->continue_button($returnurl ?: new moodle_url('/blocks/cdo_notification/'));
    echo $OUTPUT->footer();
    exit;
}

require_once($CFG->dirroot . '/blocks/cdo_notification/classes/output/notifications/main_renderable.php');
$renderable = new \block_cdo_notification\output\notifications\main_renderable($notifications, $totalcount);
$renderer = $PAGE->get_renderer('block_cdo_notification', 'notifications');
$templatecontext = $renderable->export_for_template($renderer);

// Добавляем пагинацию в шаблон
if ($totalcount > $perpage) {
    $baseurl = new moodle_url('/blocks/cdo_notification/index.php');
    $templatecontext['pagination'] = $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
    $templatecontext['showpagination'] = true;
} else {
    $templatecontext['pagination'] = '';
    $templatecontext['showpagination'] = false;
}

echo $renderer->render_from_template('block_cdo_notification/main', $templatecontext);
echo $OUTPUT->footer();
?> 
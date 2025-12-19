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
 * Notifications list page
 *
 * @package    block_cdo_notification
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

use block_cdo_notification\notification_manager;
use block_cdo_notification\output\notifications\list_renderable;

// Get parameters
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$sort = optional_param('sort', 'date', PARAM_ALPHA);
$order = optional_param('order', 'desc', PARAM_ALPHA);

// Set up page
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/cdo_notification/notifications_list.php', [
    'page' => $page,
    'perpage' => $perpage,
    'search' => $search,
    'sort' => $sort,
    'order' => $order
]);
$PAGE->set_title(get_string('notificationslist', 'block_cdo_notification'));
$PAGE->set_heading(get_string('notificationslist', 'block_cdo_notification'));
$PAGE->set_pagelayout('standard');

// Check capabilities
require_capability('block/cdo_notification:view', context_system::instance());

// Add breadcrumb navigation
$PAGE->navbar->add(get_string('pluginname', 'block_cdo_notification'), new moodle_url('/blocks/cdo_notification/'));
$PAGE->navbar->add(get_string('notificationslist', 'block_cdo_notification'));

// Get notifications data
$manager = new notification_manager();
$notifications = [];
$error = null;
$totalcount = 0;

try {
    $allnotifications = $manager->get_active_notifications();
    
    // Apply search filter if provided
    if (!empty($search)) {
        $filterednotifications = [];
        foreach ($allnotifications as $notification) {
            if (stripos($notification['header'], $search) !== false || 
                stripos($notification['body_message'], $search) !== false) {
                $filterednotifications[] = $notification;
            }
        }
        $allnotifications = $filterednotifications;
    }
    
    $totalcount = count($allnotifications);
    
    // Sort notifications
    usort($allnotifications, function($a, $b) use ($sort, $order) {
        $result = 0;
        switch ($sort) {
            case 'date':
                $result = strtotime($a['date']) <=> strtotime($b['date']);
                break;
            case 'header':
                $result = strcasecmp($a['header'], $b['header']);
                break;
            default:
                $result = strtotime($a['date']) <=> strtotime($b['date']);
        }
        return $order === 'desc' ? -$result : $result;
    });
    
    // Apply pagination
    $notifications = array_slice($allnotifications, $page * $perpage, $perpage);
    
    // Process notifications for display
    foreach ($notifications as &$notification) {
        $notification['notification-idx'] = uniqid();
        $dateTime = new DateTime($notification['date']);
        $notification['date'] = $dateTime->format('d.m.Y H:i:s');
        $notification['date_raw'] = $notification['date'];
        $notification['short_body'] = strip_tags($notification['body_message']);
        if (strlen($notification['short_body']) > 150) {
            $notification['short_body'] = substr($notification['short_body'], 0, 150) . '...';
        }
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Start output
echo $OUTPUT->header();

if ($error) {
    echo $OUTPUT->notification($error, 'error');
    echo $OUTPUT->continue_button(new moodle_url('/blocks/cdo_notification/'));
    echo $OUTPUT->footer();
    exit;
}

// Готовим данные для шаблона
$renderer = $PAGE->get_renderer('block_cdo_notification', 'notifications');
$baseurl = (new moodle_url('/blocks/cdo_notification/notifications_list.php', []))->out(false);
$showclear = !empty($search) || $sort !== 'date' || $order !== 'desc';
$showpagination = $totalcount > $perpage;
$pagination = $showpagination ? $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl) : '';

// Для mustache: выделяем выбранные опции
$sort_date = $sort === 'date';
$sort_header = $sort === 'header';
$order_desc = $order === 'desc';
$order_asc = $order === 'asc';

$renderable = new list_renderable(
    $notifications,
    $totalcount,
    $page,
    $perpage,
    $search,
    $sort,
    $order,
    $baseurl
);
$templatecontext = $renderable->export_for_template($renderer);
$templatecontext['showclear'] = $showclear;
$templatecontext['showpagination'] = $showpagination;
$templatecontext['pagination'] = $pagination;
$templatecontext['sort_date'] = $sort_date;
$templatecontext['sort_header'] = $sort_header;
$templatecontext['order_desc'] = $order_desc;
$templatecontext['order_asc'] = $order_asc;

// Рендерим шаблон
echo $renderer->render_from_template('block_cdo_notification/notifications_list', $templatecontext);

echo $OUTPUT->footer();
?> 
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
 * Error handling page for cdo_order_documents plugin
 *
 * @package   local_cdo_order_documents
 * @copyright 2024 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

global $PAGE, $OUTPUT;

$errorcode = optional_param('code', '', PARAM_TEXT);
$errormessage = optional_param('message', '', PARAM_TEXT);
$returnto = optional_param('return', 'index', PARAM_TEXT);
$plugin_name = "local_cdo_certification_sheet";
$title = get_string('error_page_title', $plugin_name);
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url('/local/cdo_certification_sheet/error.php');
$PAGE->set_heading($title);
$PAGE->set_title($title);

echo $OUTPUT->header();

// Определяем тип ошибки и соответствующее сообщение
$error_type = 'general';
$error_display_message = '';

switch ($errorcode) {
    case 'download_failed':
        $error_type = 'download';
        $error_display_message = get_string('error_download_failed', $plugin_name);
        break;
    case 'connection_error':
        $error_type = 'connection';
        $error_display_message = get_string('error_connection', $plugin_name);
        break;
    case 'invalid_certificate':
        $error_type = 'invalid';
        $error_display_message = get_string('error_invalid_certificate', $plugin_name);
        break;
    case 'permission_denied':
        $error_type = 'permission';
        $error_display_message = get_string('error_permission_denied', $plugin_name);
        break;
    default:
        $error_type = 'general';
        $error_display_message = get_string('error_general', $plugin_name);
        break;
}

// Если передано кастомное сообщение, используем его
if (!empty($errormessage)) {
    // Пытаемся декодировать JSON строку
    $decoded_message = json_decode($errormessage, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $error_display_message = $decoded_message;
    } else {
        $error_display_message = $errormessage;
    }
}

// Определяем URL для возврата
$return_url = '';
switch ($returnto) {
    case 'index':
        $return_url = new moodle_url('/local/cdo_certification_sheet/index.php');
        break;
    case 'certificates':
        $return_url = new moodle_url('/local/cdo_certification_sheet/index.php', ['show_certificates' => 1]);
        break;
    default:
        $return_url = new moodle_url('/local/cdo_certification_sheet/index.php');
        break;
}

// Отображаем страницу ошибки
?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-exclamation-triangle mr-2"></i>
                        <?php echo get_string('error_occurred', $plugin_name); ?>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        <h5 class="alert-heading">
                            <?php echo get_string('error_type_' . $error_type, $plugin_name); ?>
                        </h5>
                        <p class="mb-0"><?php echo $error_display_message; ?></p>
                    </div>
                    
                    <?php if ($error_type === 'download'): ?>
                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading"><?php echo get_string('error_troubleshooting', $plugin_name); ?></h6>
                        <ul class="mb-0">
                            <li><?php echo get_string('error_tip_network', $plugin_name); ?></li>
                            <li><?php echo get_string('error_tip_retry', $plugin_name); ?></li>
                            <li><?php echo get_string('error_tip_contact', $plugin_name); ?></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <a href="<?php echo $return_url; ?>" class="btn btn-primary mr-2">
                            <i class="fa fa-arrow-left mr-1"></i>
                            <?php echo get_string('error_return', $plugin_name); ?>
                        </a>
                        
                        <?php if ($error_type === 'download'): ?>
                        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                            <i class="fa fa-refresh mr-1"></i>
                            <?php echo get_string('error_retry', $plugin_name); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Дополнительная информация для администраторов -->
            <?php if (has_capability('moodle/site:config', $systemcontext)): ?>
            <div class="card border-warning mt-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-info-circle mr-2"></i>
                        <?php echo get_string('error_debug_info', $plugin_name); ?>
                    </h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <strong><?php echo get_string('error_code', $plugin_name); ?>:</strong> <?php echo $errorcode; ?><br>
                        <strong><?php echo get_string('error_message', $plugin_name); ?>:</strong> <?php echo $errormessage; ?><br>
                        <strong><?php echo get_string('error_time', $plugin_name); ?>:</strong> <?php echo date('Y-m-d H:i:s'); ?>
                    </small>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php

echo $OUTPUT->footer();

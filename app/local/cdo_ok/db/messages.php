<?php
/**
 * Message providers for local_cdo_ok plugin
 *
 * @package    local_cdo_ok
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$messageproviders = [
    'notify_ok' => [
        'defaults' => [
            'popup' => MESSAGE_PERMITTED,
            'email' => MESSAGE_PERMITTED
        ],
    ],
];
<?php
/**
 * Scheduled tasks for local_cdo_ok plugin
 *
 * @package    local_cdo_ok
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => 'local_cdo_ok\task\notify',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '7',
        'day' => '1',
        'month' => '*',
        'dayofweek' => '0',
    ],
];
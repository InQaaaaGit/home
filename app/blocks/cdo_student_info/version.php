<?php
/**
 * Version details.
 *
 * @package    cdostudentinfo
 * @copyright  2022 InQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$plugin->version = 2023041906; // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires = 2019111200; // Requires this Moodle version.
$plugin->component = 'block_cdo_student_info'; // Full name of the plugin (used for diagnostics).
$release  = '0.0.1 (Build: 20230419)'; // Human-friendly version name
$branch   = '001';                       // This version's branch.
$maturity = MATURITY_ALPHA;
$plugin->dependencies = [
	"tool_cdo_config" => 20220512.01
];

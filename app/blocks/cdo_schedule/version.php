<?php
/**
 * Version details.
 *
 * @package    cdostudentinfo
 * @copyright  2022 InQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$plugin->version = 2025030707;
$plugin->requires = 2019111200; // Requires this Moodle version.
$plugin->component = 'block_cdo_schedule'; // Full name of the plugin (used for diagnostics).
$release  = '0.0.6 (Build: 20250307)'; // Human-friendly version name
$branch   = '001';                       // This version's branch.
$maturity = MATURITY_ALPHA;
$plugin->dependencies = [
	"tool_cdo_config" => 20220512.01
];

<?php
/**
 * Version details.
 *
 * @package    cdostudentinfo
 * @copyright  2022 InQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$plugin->version = 2024122102; // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires = 2019111200; // Requires this Moodle version.
$plugin->component = 'block_cdo_ag_partners'; // Full name of the plugin (used for diagnostics).
$release  = '1'; // Human-friendly version name
$branch   = '1';                       // This version's branch.

$plugin->dependencies = [
	"local_cdo_ag_tools" => 2023121411
];

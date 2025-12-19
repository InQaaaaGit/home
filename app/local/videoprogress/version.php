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
 * Version details
 *
 * @package    local_videoprogress
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2024031515;        // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires  = 2022112800;        // Requires this Moodle version.
$plugin->component = 'local_videoprogress'; // Full name of the plugin (used for diagnostics).
$plugin->maturity  = MATURITY_ALPHA;    // This is considered as ALPHA for production sites.
$plugin->release   = 'v0.1.0';          // Human-readable version name.

// Регистрируем AMD-модули
$plugin->dependencies = [
    'mod_resource' => 2022112800, // Требуем модуль resource для работы с видео
];

// Определяем AMD-модули
$plugin->amd = [
    'local_videoprogress/module' => [
        'src' => 'amd/src/module',
        'deps' => ['core/ajax', 'core/str', 'local_videoprogress/videotracker']
    ],
    'local_videoprogress/videotracker' => [
        'src' => 'amd/src/videotracker',
        'deps' => ['core/ajax', 'core/str']
    ]
]; 
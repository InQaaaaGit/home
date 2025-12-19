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
 * Hook callbacks for local_cdo_visuallyimpaired plugin.
 *
 * @package   local_cdo_visuallyimpaired
 * @copyright 2023
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cdo_visuallyimpaired;

/**
 * Hook callbacks for the plugin.
 */
class hook_callbacks {
    
    /**
     * Hook to add CSS files to the page before HTTP headers are sent.
     *
     * @param \core\hook\output\before_http_headers $hook
     * @return void
     */
    public static function before_http_headers(\core\hook\output\before_http_headers $hook): void {
        global $PAGE, $CFG;
        
        // Проверяем, что плагин включен
        if (empty($CFG->local_cdo_visuallyimpaired_enabled)) {
            return;
        }
        
        // Проверяем, что мы не на странице администрирования или в AJAX запросе
        if ($PAGE->pagetype === 'admin-index' || AJAX_SCRIPT) {
            return;
        }
        
        // Подключаем CSS файл BVI плагина из папки plugins
        $PAGE->requires->css('/local/cdo_visuallyimpaired/plugins/dist/css/bvi.min.css');
    }
}


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
 * Library of functions for local_cdo_visuallyimpaired plugin.
 *
 * @package   local_cdo_visuallyimpaired
 * @copyright 2023
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Hook to add CSS and JS files to the page.
 * 
 * DEPRECATED: This function is kept for backward compatibility with older Moodle versions.
 * For Moodle 4.3+, the hook callback is registered in db/hooks.php and implemented
 * in classes/hook_callbacks.php using the new hook system.
 *
 * @deprecated since version 2.1
 * @see \local_cdo_visuallyimpaired\hook_callbacks::before_http_headers()
 * @return void
 */
function local_cdo_visuallyimpaired_before_http_headers() {
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

/**
 * Hook to add JavaScript files to the page.
 *
 * @return void
 */
function local_cdo_visuallyimpaired_before_footer() {
    global $PAGE, $CFG;
    
    // Проверяем, что плагин включен
    if (empty($CFG->local_cdo_visuallyimpaired_enabled)) {
        return;
    }
    
    // Проверяем, что мы не на странице администрирования или в AJAX запросе
    if ($PAGE->pagetype === 'admin-index' || AJAX_SCRIPT) {
        return;
    }
    
    // Получаем настройки из админки
    $fontsize = get_config('local_cdo_visuallyimpaired', 'fontsize') ?: '16';
    $theme = get_config('local_cdo_visuallyimpaired', 'theme') ?: 'white';
    $speech = get_config('local_cdo_visuallyimpaired', 'speech') ? 'true' : 'false';
    $images = get_config('local_cdo_visuallyimpaired', 'images') ?: 'grayscale';
    
    // Подключаем BVI плагин из папки plugins
    $PAGE->requires->js('/local/cdo_visuallyimpaired/plugins/dist/js/bvi.min.js', true);
    
    // Подключаем наш инициализационный скрипт с настройками
    $PAGE->requires->js_init_code('
        document.addEventListener("DOMContentLoaded", function() {
            var checkBVI = setInterval(function() {
                if (typeof window.isvek !== "undefined" && window.isvek.Bvi) {
                    clearInterval(checkBVI);
                    
                    new window.isvek.Bvi({
                        target: ".bvi-open",
                        fontSize: ' . intval($fontsize) . ',
                        theme: "' . $theme . '",
                        images: "' . $images . '",
                        letterSpacing: "normal",
                        lineHeight: "normal",
                        speech: ' . $speech . ',
                        fontFamily: "arial",
                        builtElements: false,
                        panelFixed: true,
                        panelHide: false,
                        reload: false,
                        lang: "ru-RU"
                    });
                    
                    // Добавляем плавающую кнопку
                    if (!document.getElementById("cdo-floating-accessibility-btn")) {
                        var floatingBtn = document.createElement("div");
                        floatingBtn.id = "cdo-floating-accessibility-btn";
                        floatingBtn.innerHTML = 
                            "<button class=\\"bvi-open\\" style=\\"" +
                            "position: fixed; top: 20px; right: 20px; z-index: 999999; " +
                            "background: #007bff; color: white; border: none; " +
                            "padding: 12px 16px; border-radius: 50px; cursor: pointer; " +
                            "font-size: 14px; font-weight: bold; " +
                            "box-shadow: 0 4px 12px rgba(0,123,255,0.3); " +
                            "transition: all 0.3s ease; display: flex; " +
                            "align-items: center; gap: 8px;\\">" +
                            "<span style=\\"font-size: 18px;\\">👁️</span>" +
                            "<span>Доступность</span>" +
                            "</button>";
                        document.body.appendChild(floatingBtn);
                    }
                }
            }, 100);
        });
    ');
    
    // Подключаем файл отладки только в режиме разработки
    if (!empty($CFG->debugdeveloper)) {
        $PAGE->requires->js('/local/cdo_visuallyimpaired/js/debug-simple.js', true);
    }
}

/**
 * AMD модуль для инициализации плагина доступности
 */
define(['jquery', 'local_cdo_visuallyimpaired/init'], function($, AccessibilityPlugin) {
    'use strict';
    
    return {
        init: function() {
            // Инициализируем плагин после загрузки DOM
            $(document).ready(function() {
                // Добавляем небольшую задержку для избежания конфликтов
                setTimeout(function() {
                    AccessibilityPlugin.init();
                }, 100);
            });
        }
    };
});

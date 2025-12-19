/**
 * Обертка для BVI плагина для совместимости с Moodle
 */
define(['jquery'], function($) {
    'use strict';
    
    // Загружаем BVI плагин через script tag для избежания конфликтов с AMD
    return {
        init: function() {
            return new Promise(function(resolve, reject) {
                // Проверяем, не загружен ли уже BVI
                if (typeof window.isvek !== 'undefined' && window.isvek.Bvi) {
                    resolve(window.isvek.Bvi);
                    return;
                }
                
                // Создаем script элемент для загрузки BVI
                var script = document.createElement('script');
                script.src = M.cfg.wwwroot + '/local/cdo_visuallyimpaired/js/bvi.min.js';
                script.onload = function() {
                    if (typeof window.isvek !== 'undefined' && window.isvek.Bvi) {
                        resolve(window.isvek.Bvi);
                    } else {
                        reject(new Error('BVI плагин не загрузился'));
                    }
                };
                script.onerror = function() {
                    reject(new Error('Ошибка загрузки BVI плагина'));
                };
                
                document.head.appendChild(script);
            });
        }
    };
});

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
 * @module     local_videoprogress/module
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/ajax', 'core/str', 'local_videoprogress/videotracker'], function(Ajax, Str, VideoTracker) {
    // Конфигурация для разработки
    const config = {
        debug: true, // Включаем режим отладки
        updateInterval: 5000, // Интервал обновления по умолчанию
        logLevel: 'debug' // Уровень логирования
    };

    /**
     * Получение cmid из URL
     * @returns {string|null} cmid или null
     */
    function getCmidFromUrl() {
        try {
            const url = new URL(window.location.href);
            const cmid = url.searchParams.get('id');
            if (config.debug) {
                console.log('VideoTracker: Extracted cmid from URL:', cmid);
            }
            return cmid;
        } catch (error) {
            if (config.debug) {
                console.warn('VideoTracker: Error extracting cmid from URL:', error);
            }
            return null;
        }
    }

    /**
     * Инициализация трекера видео
     * @param {Object} options Опции конфигурации
     */
    function init(options = {}) {
        if (config.debug) {
            console.log('VideoTracker: Initializing with options:', options);
        }

        // Объединяем опции по умолчанию с переданными
        const trackerOptions = {
            ...config,
            ...options
        };

        // Ищем все видео элементы на странице
        const videoElements = document.querySelectorAll('video');
        
        if (config.debug) {
            console.log('VideoTracker: Found video elements:', videoElements.length);
        }

        videoElements.forEach(function(videoElement) {
            try {
                // Получаем cmid из URL или data-атрибута
                const cmid = options.cmid || 
                            videoElement.closest('[data-cmid]')?.dataset.cmid ||
                            getCmidFromUrl();

                if (!cmid) {
                    if (config.debug) {
                        console.warn('VideoTracker: No cmid found for video element');
                    }
                    return;
                }
                console.warn(videoElement);
                // Генерируем уникальный videoid
                let videoid = videoElement.id;

                if (config.debug) {
                    console.log('VideoTracker: Initializing for video:', {
                        videoid,
                        cmid,
                        element: videoElement
                    });
                }

                // Создаем и инициализируем трекер
                const tracker = new VideoTracker({
                    videoid,
                    cmid: parseInt(cmid),
                    updateInterval: trackerOptions.updateInterval,
                    debug: config.debug
                });

                tracker.init(videoElement);

            } catch (error) {
                console.error('VideoTracker: Error initializing tracker:', error);
            }
        });
    }

    /**
     * Обновление конфигурации
     * @param {Object} newConfig Новая конфигурация
     */
    function setConfig(newConfig) {
        Object.assign(config, newConfig);
        if (config.debug) {
            console.log('VideoTracker: Updated config:', config);
        }
    }

    return {
        init: init,
        setConfig: setConfig
    };
}); 
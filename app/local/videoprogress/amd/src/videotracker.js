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
 * @module     local_videoprogress/videotracker
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/ajax', 'core/str'], function(Ajax, Str) {
    
    /**
     * Extract clean video name from URL
     * @param {string} url Video URL
     * @param {boolean} debug Enable debug logging
     * @returns {string|null} Clean video name
     */
    function extractVideoNameFromUrl(url, debug = false) {
        if (!url) return null;
        
        try {
            // Создаем URL объект для правильного парсинга
            const urlObj = new URL(url);
            
            // Извлекаем путь без параметров
            let pathname = urlObj.pathname;
            
            // Получаем только название файла
            const parts = pathname.split('/');
            let filename = parts[parts.length - 1];
            
            // Убираем расширение файла
            filename = filename.replace(/\.[^/.]+$/, "");
            
            // Декодируем URL-кодированные символы
            filename = decodeURIComponent(filename);
            
            if (debug) {
                console.log('VideoTracker: Extracted video name:', {
                    originalUrl: url,
                    pathname: pathname,
                    filename: filename
                });
            }
            
            return filename || null;
        } catch (error) {
            // Fallback для относительных URL или некорректных URL
            const parts = url.split('/');
            let filename = parts[parts.length - 1];
            
            // Убираем параметры запроса если есть
            filename = filename.split('?')[0];
            filename = filename.split('#')[0];
            
            // Убираем расширение файла
            filename = filename.replace(/\.[^/.]+$/, "");
            
            // Декодируем URL-кодированные символы
            try {
                filename = decodeURIComponent(filename);
            } catch (decodeError) {
                // Если не удается декодировать, оставляем как есть
            }
            
            if (debug) {
                console.log('VideoTracker: Extracted video name (fallback):', {
                    originalUrl: url,
                    filename: filename,
                    error: error.message
                });
            }
            
            return filename || null;
        }
    }

    /**
     * Get video name from source elements only
     * @param {HTMLVideoElement} videoElement Video element
     * @param {boolean} debug Enable debug logging
     * @returns {string|null} Video name from source src
     */
    function getVideoNameFromSource(videoElement, debug = false) {
        if (!videoElement) return null;
        
        // Ищем элементы source внутри видео
        const sourceElements = videoElement.querySelectorAll('source');
        if (sourceElements.length > 0) {
            // Берем первый source элемент
            const firstSource = sourceElements[0];
            const src = firstSource.src;
            if (src) {
                return extractVideoNameFromUrl(src, debug);
            }
        }
        
        return null;
    }

    /**
     * Video progress tracker class
     */
    class VideoTracker {
        /**
         * @param {Object} options Options for the tracker
         * @param {number} options.cmid Course module ID
         * @param {number} [options.updateInterval=5000] Update interval in milliseconds
         * @param {boolean} [options.debug=false] Enable debug mode
         */
        constructor(options) {
            this.cmid = options.cmid;
            this.updateInterval = options.updateInterval || 5000;
            this.debug = options.debug || false;
            this.videoElement = null;
            this.updateTimer = null;
            this.lastProgress = 0;
            this.lastUpdateTime = 0;
            this.watchedSegments = [];
            this.segmentSize = 5; // секунд
            
            // Видео имя будет определено в init()
            this._videoName = null;

            if (this.debug) {
                console.log('VideoTracker: Created instance with options:', {
                    cmid: this.cmid,
                    updateInterval: this.updateInterval
                });
            }
        }

        /**
         * Initialize the tracker for a video element
         * @param {HTMLVideoElement} videoElement The video element to track
         */
        init(videoElement) {
            this.videoElement = videoElement;
            
            // Получаем название видео только из source элементов
            this._videoName = getVideoNameFromSource(videoElement, this.debug);
            
            if (this.debug) {
                const sourceElements = videoElement.querySelectorAll('source');
                const sourceInfo = Array.from(sourceElements).map(source => ({
                    src: source.src,
                    type: source.type
                }));
                
                console.log('VideoTracker: Initializing for element:', {
                    element: videoElement,
                    sourceElements: sourceInfo,
                    extractedVideoName: this._videoName,
                    duration: videoElement.duration
                });
            }

            // Добавляем обработчики событий
            this.videoElement.addEventListener('play', () => {
                if (this.debug) console.log('VideoTracker: Video started playing');
                this.startTracking();
            });

            this.videoElement.addEventListener('pause', () => {
                if (this.debug) console.log('VideoTracker: Video paused');
                this.stopTracking();
            });

            this.videoElement.addEventListener('ended', () => {
                if (this.debug) console.log('VideoTracker: Video ended');
                this.updateSegments();
            });

            this.videoElement.addEventListener('timeupdate', () => {
                this.updateWatchedSegments();
                const progress = this.calculateProgress();
                if (this.debug && progress > this.lastProgress) {
                    console.log('VideoTracker: Progress updated:', progress);
                }
            });

            // Проверяем, не воспроизводится ли видео уже
            if (!this.videoElement.paused) {
                if (this.debug) console.log('VideoTracker: Video already playing, starting tracking');
                this.startTracking();
            }
        }

        /**
         * Start tracking video progress
         */
        startTracking() {
            if (this.updateTimer) {
                if (this.debug) console.log('VideoTracker: Tracking already started');
                return;
            }

            if (this.debug) console.log('VideoTracker: Starting tracking');

            this.updateTimer = setInterval(() => {
                const progress = this.calculateProgress();
                const now = Date.now();
                
                // Обновляем сегменты если прогресс увеличился или прошло достаточно времени
                if (progress > this.lastProgress || (now - this.lastUpdateTime) >= this.updateInterval) {
                    this.updateSegments();
                    this.lastUpdateTime = now;
                }
            }, this.updateInterval);
        }

        /**
         * Stop tracking video progress
         */
        stopTracking() {
            if (this.updateTimer) {
                if (this.debug) console.log('VideoTracker: Stopping tracking');
                clearInterval(this.updateTimer);
                this.updateTimer = null;
                
                // Отправляем финальное обновление сегментов при остановке
                this.updateSegments();
            }
        }

        /**
         * Update watched segments (local tracking only)
         */
        updateWatchedSegments() {
            if (!this.videoElement || !this.videoElement.duration) return;
            
            const current = this.videoElement.currentTime;
            const segSize = this.segmentSize;
            const segIdx = Math.floor(current / segSize);
            const segStart = segIdx * segSize;
            const segEnd = Math.min(segStart + segSize, this.videoElement.duration);
            
            // Проверяем, есть ли уже такой сегмент
            const segmentExists = this.watchedSegments.some(s => 
                Math.abs(s[0] - segStart) < 0.1 && Math.abs(s[1] - segEnd) < 0.1
            );

            if (!segmentExists) {
                this.watchedSegments.push([segStart, segEnd]);
                this.mergeSegments();
                
                if (this.debug) {
                    console.log('VideoTracker: New segment added:', {
                        segment: [segStart, segEnd],
                        allSegments: this.watchedSegments,
                        currentTime: current,
                        duration: this.videoElement.duration
                    });
                }
            }
        }

        /**
         * Merge overlapping segments
         */
        mergeSegments() {
            if (this.watchedSegments.length < 2) return;
            
            // Сортируем сегменты по начальному времени
            this.watchedSegments.sort((a, b) => a[0] - b[0]);
            
            const merged = [this.watchedSegments[0]];
            
            for (let i = 1; i < this.watchedSegments.length; i++) {
                const last = merged[merged.length - 1];
                const curr = this.watchedSegments[i];
                
                // Если текущий сегмент перекрывается с последним объединенным
                if (curr[0] <= last[1] + 0.1) { // Добавляем небольшую погрешность
                    last[1] = Math.max(last[1], curr[1]);
                } else {
                    merged.push(curr);
                }
            }
            
            this.watchedSegments = merged;
            
            if (this.debug) {
                console.log('VideoTracker: Segments merged:', {
                    segments: this.watchedSegments
                });
            }
        }

        /**
         * Calculate current video progress based on watched segments
         * @returns {number} Progress percentage
         */
        calculateProgress() {
            if (!this.videoElement || !this.videoElement.duration) {
                if (this.debug) console.warn('VideoTracker: No video element or duration available');
                return 0;
            }

            let totalWatched = 0;
            for (const [start, end] of this.watchedSegments) {
                totalWatched += end - start;
            }

            const progress = (totalWatched / this.videoElement.duration) * 100;
            const roundedProgress = Math.min(Math.round(progress * 100) / 100, 100);

            if (this.debug) {
                console.log('VideoTracker: Progress calculation:', {
                    totalWatched,
                    duration: this.videoElement.duration,
                    segments: this.watchedSegments,
                    progress: roundedProgress
                });
            }

            return roundedProgress;
        }

        /**
         * Update progress on the server
         * @param {number} progress Progress percentage
         */
        updateProgress(progress) {
            const currentVideoName = this.videoid; // Получаем актуальное значение через getter
            
            if (this.debug) {
                console.log('VideoTracker: Updating progress:', {
                    progress,
                    videoName: currentVideoName,
                    cmid: this.cmid
                });
            }

            Ajax.call([{
                methodname: 'local_videoprogress_update_progress',
                args: {
                    videoid: currentVideoName,
                    cmid: this.cmid,
                    progress: progress
                }
            }])[0]
            .then(result => {
                if (this.debug) {
                    console.log('VideoTracker: Progress update result:', result);
                }
                this.lastProgress = progress;
            })
            .catch(error => {
                console.error('VideoTracker: Failed to update progress:', error);
                if (this.debug) {
                    console.error('VideoTracker: Error details:', {
                        error,
                        videoName: currentVideoName,
                        cmid: this.cmid,
                        progress
                    });
                }
            });
        }

        /**
         * Update watched segments on the server
         */
        updateSegments() {
            const currentVideoName = this.videoid;
            const duration = this.videoElement ? this.videoElement.duration : 0;
            
            if (!duration || this.watchedSegments.length === 0) {
                if (this.debug) {
                    console.log('VideoTracker: Skipping segments update - no duration or segments');
                }
                return;
            }
            
            if (this.debug) {
                console.log('VideoTracker: Updating segments:', {
                    videoName: currentVideoName,
                    cmid: this.cmid,
                    segments: this.watchedSegments,
                    duration: duration
                });
            }

            Ajax.call([{
                methodname: 'local_videoprogress_update_segments',
                args: {
                    videoid: currentVideoName,
                    cmid: this.cmid,
                    segments: this.watchedSegments,
                    duration: duration
                }
            }])[0]
            .then(result => {
                if (this.debug) {
                    console.log('VideoTracker: Segments update result:', result);
                }
                this.lastProgress = result.progress || 0;
            })
            .catch(error => {
                console.error('VideoTracker: Failed to update segments:', error);
                if (this.debug) {
                    console.error('VideoTracker: Segments update error details:', {
                        error,
                        videoName: currentVideoName,
                        cmid: this.cmid,
                        segments: this.watchedSegments,
                        duration: duration
                    });
                }
            });
        }

        /**
         * Get current video ID/name
         * @returns {string} Current video ID/name
         */
        get videoid() {
            return this._videoName || 'unknown_video';
        }
    }

    // Возвращаем класс напрямую для обратной совместимости
    return VideoTracker;
}); 
/**
 * Файл отладки для плагина доступности
 * Помогает выявить конфликты с другими модулями
 */
define(['jquery'], function($) {
    'use strict';
    
    var DebugModule = {
        init: function() {
            console.log('CDO Visually Impaired Plugin: Инициализация отладки');
            
            // Проверяем наличие конфликтующих элементов
            this.checkConflicts();
            
            // Мониторим ошибки JavaScript
            this.monitorErrors();
            
            // Проверяем загрузку BVI плагина
            this.checkBVILoading();
        },
        
        checkConflicts: function() {
            var conflicts = [];
            
            // Проверяем, не переопределяют ли другие модули наши функции
            if (typeof window.isvek !== 'undefined' && window.isvek.Bvi) {
                console.log('CDO Visually Impaired: BVI плагин найден');
            } else {
                console.warn('CDO Visually Impaired: BVI плагин не найден');
            }
            
            // Проверяем наличие элементов с нашими классами
            var bviElements = document.querySelectorAll('.bvi-open');
            console.log('CDO Visually Impaired: Найдено элементов .bvi-open:', bviElements.length);
            
            // Проверяем, не конфликтуют ли наши ID с другими
            var specialButton = document.getElementById('specialButton');
            if (specialButton) {
                console.log('CDO Visually Impaired: Элемент #specialButton найден');
            }
            
            // Проверяем плавающую кнопку
            var floatingBtn = document.getElementById('cdo-floating-accessibility-btn');
            if (floatingBtn) {
                console.log('CDO Visually Impaired: Плавающая кнопка найдена');
            }
        },
        
        checkBVILoading: function() {
            var self = this;
            var attempts = 0;
            var maxAttempts = 50; // 5 секунд
            
            var checkInterval = setInterval(function() {
                attempts++;
                
                if (typeof window.isvek !== 'undefined' && window.isvek.Bvi) {
                    console.log('CDO Visually Impaired: BVI плагин успешно загружен');
                    clearInterval(checkInterval);
                } else if (attempts >= maxAttempts) {
                    console.warn('CDO Visually Impaired: BVI плагин не загрузился в течение 5 секунд');
                    clearInterval(checkInterval);
                }
            }, 100);
        },
        
        monitorErrors: function() {
            var originalError = window.onerror;
            window.onerror = function(message, source, lineno, colno, error) {
                // Проверяем, связана ли ошибка с нашим плагином
                if (source && source.includes('cdo_visuallyimpaired')) {
                    console.error('CDO Visually Impaired Plugin Error:', {
                        message: message,
                        source: source,
                        line: lineno,
                        column: colno,
                        error: error
                    });
                }
                
                // Вызываем оригинальный обработчик ошибок
                if (originalError) {
                    return originalError.apply(this, arguments);
                }
                return false;
            };
        }
    };
    
    return DebugModule;
});

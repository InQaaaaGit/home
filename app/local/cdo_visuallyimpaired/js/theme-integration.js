/**
 * Интеграция плагина доступности в тему Moodle
 * Добавляет кнопку доступности в существующий интерфейс
 */
(function() {
    'use strict';
    
    var AccessibilityThemeIntegration = {
        
        init: function() {
            this.addAccessibilityButton();
            this.initializeBVI();
        },
        
        /**
         * Добавляет кнопку доступности в интерфейс Moodle
         */
        addAccessibilityButton: function() {
            try {
                // Ищем контейнер usernavigation
                var userNav = document.getElementById('usernavigation');
                if (!userNav) {
                    console.warn('Контейнер #usernavigation не найден');
                    return;
                }
                
                // Проверяем, не добавлена ли уже кнопка
                if (document.getElementById('accessibility-button-container')) {
                    return;
                }
                
                // Создаем контейнер для кнопки доступности
                var accessibilityContainer = document.createElement('div');
                accessibilityContainer.id = 'accessibility-button-container';
                accessibilityContainer.className = 'd-flex align-items-stretch';
                accessibilityContainer.style.cssText = 'margin-right: 10px;';
                
                // Создаем кнопку доступности в стиле Moodle
                var accessibilityButton = document.createElement('div');
                accessibilityButton.className = 'popover-region collapsed';
                accessibilityButton.innerHTML = 
                    '<a href="#" class="nav-link popover-region-toggle position-relative icon-no-margin bvi-open" ' +
                    'role="button" aria-label="Доступность для слабовидящих" title="Доступность для слабовидящих">' +
                    '<i class="icon fa fa-eye fa-fw" title="Доступность" role="img" aria-label="Доступность"></i>' +
                    '</a>';
                
                accessibilityContainer.appendChild(accessibilityButton);
                
                // Вставляем кнопку после usernavigation
                userNav.parentNode.insertBefore(accessibilityContainer, userNav.nextSibling);
                
                console.log('Кнопка доступности добавлена в интерфейс Moodle');
                
            } catch (error) {
                console.warn('Ошибка добавления кнопки доступности:', error);
            }
        },
        
        /**
         * Инициализирует BVI плагин
         */
        initializeBVI: function() {
            var self = this;
            var attempts = 0;
            var maxAttempts = 100; // 10 секунд
            
            var checkBVI = setInterval(function() {
                attempts++;
                
                if (typeof window.isvek !== 'undefined' && window.isvek.Bvi) {
                    clearInterval(checkBVI);
                    
                    try {
                        // Инициализируем BVI плагин
                        new window.isvek.Bvi({
                            target: '.bvi-open',
                            fontSize: 16,
                            theme: 'white',
                            images: 'grayscale',
                            letterSpacing: 'normal',
                            lineHeight: 'normal',
                            speech: true,
                            fontFamily: 'arial',
                            builtElements: false,
                            panelFixed: true,
                            panelHide: false,
                            reload: false,
                            lang: 'ru-RU'
                        });
                        console.log('BVI плагин успешно инициализирован в теме');
                        
                    } catch (bviError) {
                        console.warn('Ошибка инициализации BVI плагина:', bviError);
                    }
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkBVI);
                    console.warn('BVI плагин не загрузился в течение 10 секунд');
                    self.showFallbackMessage();
                }
            }, 100);
        },
        
        /**
         * Показывает сообщение о недоступности плагина
         */
        showFallbackMessage: function() {
            try {
                var message = document.createElement('div');
                message.style.cssText = 
                    'position: fixed; top: 20px; right: 20px; z-index: 999999; ' +
                    'background: #dc3545; color: white; padding: 10px 15px; ' +
                    'border-radius: 5px; font-size: 12px; max-width: 200px;';
                message.innerHTML = 'Плагин доступности временно недоступен';
                document.body.appendChild(message);
                
                // Убираем сообщение через 5 секунд
                setTimeout(function() {
                    if (message.parentNode) {
                        message.parentNode.removeChild(message);
                    }
                }, 5000);
            } catch (error) {
                console.warn('Ошибка показа сообщения:', error);
            }
        }
    };
    
    // Инициализируем плагин после загрузки DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                AccessibilityThemeIntegration.init();
            }, 500); // Увеличиваем задержку для полной загрузки интерфейса
        });
    } else {
        setTimeout(function() {
            AccessibilityThemeIntegration.init();
        }, 500);
    }
    
})();

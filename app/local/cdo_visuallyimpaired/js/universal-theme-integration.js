/**
 * Универсальная интеграция плагина доступности для разных тем Moodle
 * Поддерживает различные темы и добавляет кнопку в подходящее место
 */
(function() {
    'use strict';
    
    var UniversalAccessibilityIntegration = {
        
        // Список селекторов для разных тем Moodle
        selectors: [
            // Стандартная тема Boost
            {
                container: '#usernavigation',
                position: 'after',
                description: 'Boost theme - usernavigation'
            },
            // Тема с header-tools
            {
                container: '.header-tools.type-text.tools-pos2',
                position: 'after',
                description: 'Custom theme - header-tools'
            },
            // Альтернативные селекторы
            {
                container: '.navbar-nav.ms-auto',
                position: 'after',
                description: 'Bootstrap navbar'
            },
            {
                container: '.usermenu-container',
                position: 'before',
                description: 'User menu container'
            },
            {
                container: '.popover-region-notifications',
                position: 'after',
                description: 'After notifications'
            },
            // Fallback - в body
            {
                container: 'body',
                position: 'prepend',
                description: 'Fallback - body'
            }
        ],
        
        init: function() {
            this.addAccessibilityButton();
            this.initializeBVI();
        },
        
        /**
         * Добавляет кнопку доступности в подходящее место
         */
        addAccessibilityButton: function() {
            try {
                // Проверяем, не добавлена ли уже кнопка
                if (document.getElementById('universal-accessibility-btn')) {
                    return;
                }
                
                var buttonAdded = false;
                
                // Пробуем каждый селектор по порядку
                for (var i = 0; i < this.selectors.length; i++) {
                    var selector = this.selectors[i];
                    var container = document.querySelector(selector.container);
                    
                    if (container) {
                        console.log('Найден контейнер:', selector.description, selector.container);
                        
                        if (this.insertButton(container, selector.position, selector.description)) {
                            buttonAdded = true;
                            break;
                        }
                    }
                }
                
                if (!buttonAdded) {
                    console.warn('Не удалось найти подходящий контейнер для кнопки доступности');
                    this.addFloatingButton();
                }
                
            } catch (error) {
                console.warn('Ошибка добавления кнопки доступности:', error);
                this.addFloatingButton();
            }
        },
        
        /**
         * Вставляет кнопку в указанный контейнер
         */
        insertButton: function(container, position, description) {
            try {
                var button = this.createAccessibilityButton();
                
                switch (position) {
                    case 'after':
                        container.parentNode.insertBefore(button, container.nextSibling);
                        break;
                    case 'before':
                        container.parentNode.insertBefore(button, container);
                        break;
                    case 'prepend':
                        container.insertBefore(button, container.firstChild);
                        break;
                    case 'append':
                        container.appendChild(button);
                        break;
                    default:
                        container.parentNode.insertBefore(button, container.nextSibling);
                }
                
                console.log('Кнопка доступности добавлена:', description);
                return true;
                
            } catch (error) {
                console.warn('Ошибка вставки кнопки в контейнер:', description, error);
                return false;
            }
        },
        
        /**
         * Создает кнопку доступности
         */
        createAccessibilityButton: function() {
            var button = document.createElement('div');
            button.id = 'universal-accessibility-btn';
            button.className = 'd-flex align-items-stretch';
            button.style.cssText = 'margin-right: 10px;';
            
            // Определяем стиль кнопки в зависимости от темы
            var buttonStyle = this.detectThemeStyle();
            
            button.innerHTML = 
                '<div class="popover-region collapsed">' +
                '<a href="#" class="nav-link popover-region-toggle position-relative icon-no-margin bvi-open" ' +
                'role="button" aria-label="Доступность для слабовидящих" title="Доступность для слабовидящих">' +
                '<i class="icon fa fa-eye fa-fw" title="Доступность" role="img" aria-label="Доступность"></i>' +
                '</a></div>';
            
            return button;
        },
        
        /**
         * Определяет стиль темы и адаптирует кнопку
         */
        detectThemeStyle: function() {
            // Проверяем наличие различных классов тем
            if (document.body.classList.contains('theme-boost')) {
                return 'boost-style';
            } else if (document.body.classList.contains('theme-classic')) {
                return 'classic-style';
            } else if (document.querySelector('.header-tools')) {
                return 'custom-theme-style';
            }
            return 'default-style';
        },
        
        /**
         * Добавляет плавающую кнопку как fallback
         */
        addFloatingButton: function() {
            try {
                var floatingBtn = document.createElement('div');
                floatingBtn.id = 'floating-accessibility-btn';
                floatingBtn.style.cssText = 
                    'position: fixed; top: 20px; right: 20px; z-index: 999999; ' +
                    'background: #007bff; color: white; border: none; ' +
                    'padding: 12px 16px; border-radius: 50px; cursor: pointer; ' +
                    'font-size: 14px; font-weight: bold; ' +
                    'box-shadow: 0 4px 12px rgba(0,123,255,0.3); ' +
                    'transition: all 0.3s ease; display: flex; ' +
                    'align-items: center; gap: 8px;';
                
                floatingBtn.innerHTML = 
                    '<span style="font-size: 18px;">👁️</span>' +
                    '<span>Доступность</span>';
                
                floatingBtn.className = 'bvi-open';
                document.body.appendChild(floatingBtn);
                
                console.log('Добавлена плавающая кнопка доступности');
                
            } catch (error) {
                console.warn('Ошибка создания плавающей кнопки:', error);
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
                        console.log('BVI плагин успешно инициализирован');
                        
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
                UniversalAccessibilityIntegration.init();
            }, 500);
        });
    } else {
        setTimeout(function() {
            UniversalAccessibilityIntegration.init();
        }, 500);
    }
    
})();

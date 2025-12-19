/**
 * Простая инициализация плагина доступности без AMD
 */
(function() {
    'use strict';
    
    var AccessibilityPlugin = {
        
        init: function() {
            this.createElement();
            this.initializeBVI();
        },
        
        /**
         * Создает элементы интерфейса для кнопки доступности
         */
        createElement: function() {
            try {
                // Проверяем существование элементов перед их использованием
                var ruiIconMenus = document.getElementsByClassName('rui-icon-menu');
                if (ruiIconMenus.length > 0) {
                    var adminElement = document.querySelector('.rui-icon-menu-admin');
                    if (adminElement) {
                        for (var i = 0; i < ruiIconMenus.length; i++) {
                            ruiIconMenus[i].insertBefore(this.getVisuallyImpaired(true), adminElement);
                        }
                    }
                }

                var userNavigation = document.querySelectorAll('#usernavigation');
                if (userNavigation.length > 0) {
                    for (var j = 0; j < userNavigation.length; j++) {
                        userNavigation[j].prepend(this.getVisuallyImpaired());
                    }
                }

                var mainNavigation = document.querySelectorAll('#main-navigation ul.mb2mm');
                if (mainNavigation.length > 0) {
                    for (var k = 0; k < mainNavigation.length; k++) {
                        mainNavigation[k].append(this.getVisuallyImpaired(true));
                    }
                }

                if (window.location.pathname.includes("/login/")) {
                    var loginContent = document.getElementsByClassName('rui-login-content');
                    if (loginContent.length > 0) {
                        for (var l = 0; l < loginContent.length; l++) {
                            loginContent[l].append(this.getVisuallyImpairedLogin());
                        }
                    }
                }
            } catch (error) {
                console.warn('Ошибка создания элементов доступности:', error);
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
                        // Инициализируем BVI плагин с настройками для видимой панели
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
                            panelHide: false, // Панель всегда видна
                            reload: false,
                            lang: 'ru-RU'
                        });
                        console.log('BVI плагин успешно инициализирован');
                        
                        // Добавляем дополнительную кнопку в верхней части страницы
                        self.addFloatingButton();
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
         * Добавляет плавающую кнопку доступности
         */
        addFloatingButton: function() {
            try {
                // Проверяем, не существует ли уже такая кнопка
                if (document.getElementById('cdo-floating-accessibility-btn')) {
                    return;
                }

                var floatingBtn = document.createElement('div');
                floatingBtn.id = 'cdo-floating-accessibility-btn';
                floatingBtn.innerHTML = 
                    '<button class="bvi-open" style="' +
                    'position: fixed; top: 20px; right: 20px; z-index: 999999; ' +
                    'background: #007bff; color: white; border: none; ' +
                    'padding: 12px 16px; border-radius: 50px; cursor: pointer; ' +
                    'font-size: 14px; font-weight: bold; ' +
                    'box-shadow: 0 4px 12px rgba(0,123,255,0.3); ' +
                    'transition: all 0.3s ease; display: flex; ' +
                    'align-items: center; gap: 8px;" ' +
                    'onmouseover="this.style.transform=\'scale(1.05)\'" ' +
                    'onmouseout="this.style.transform=\'scale(1)\'">' +
                    '<span style="font-size: 18px;">👁️</span>' +
                    '<span>Доступность</span>' +
                    '</button>';
                
                document.body.appendChild(floatingBtn);
                console.log('Плавающая кнопка доступности добавлена');
            } catch (error) {
                console.warn('Ошибка создания плавающей кнопки:', error);
            }
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
        },

        getVisuallyImpaired: function(isLi, isHide) {
            try {
                var visuallyImpairedIcon = document.createElement("img");
                visuallyImpairedIcon.src = "https://lidrekon.ru/images/special.png";
                visuallyImpairedIcon.alt = "Доступность";
                visuallyImpairedIcon.style.cssText = "width: 20px; opacity: 70%;";

                if (isLi) {
                    var visuallyImpaired = document.createElement("li");
                    visuallyImpaired.className = "rui-icon-menu-admin";
                    if (isHide) {
                        visuallyImpaired.style.cssText = "display: none";
                    }
                    visuallyImpaired.id = "cdo-visually-impaired";

                    var visuallyImpairedLink = document.createElement("a");
                    visuallyImpairedLink.className = "rui-topbar-special-btn nav-link bvi-open";
                    visuallyImpairedLink.href = "#";
                    visuallyImpairedLink.id = "specialButton";
                    visuallyImpairedLink.title = "Версия для слабовидящих";

                    visuallyImpairedLink.appendChild(visuallyImpairedIcon);
                    visuallyImpaired.appendChild(visuallyImpairedLink);

                    return visuallyImpaired;
                }

                var div = document.createElement("div");
                div.className = "popover-region collapsed";
                div.id = "specialButton";
                if (isHide) {
                    div.style.cssText = "display: none";
                }

                var divContainer = document.createElement("div");
                divContainer.className = "nav-link popover-region-toggle position-relative icon-no-margin bvi-open";
                divContainer.title = "Версия для слабовидящих";
                divContainer.appendChild(visuallyImpairedIcon);
                div.appendChild(divContainer);

                return div;
            } catch (error) {
                console.warn('Ошибка создания элемента доступности:', error);
                return document.createElement('div');
            }
        },

        getVisuallyImpairedLogin: function() {
            try {
                var visuallyImpairedIcon = document.createElement("img");
                visuallyImpairedIcon.src = "https://lidrekon.ru/images/special.png";
                visuallyImpairedIcon.alt = "Доступность";
                visuallyImpairedIcon.style.cssText = "width: 20px; opacity: 70%;";

                var div = document.createElement("div");
                div.className = "popover-region collapsed";
                div.id = "specialButton";
                div.style.cssText = "display: flex; justify-content: center; margin-top: 1rem;";

                var divContainer = document.createElement("div");
                divContainer.className = "nav-link popover-region-toggle position-relative icon-no-margin bvi-open";
                divContainer.title = "Версия для слабовидящих";
                divContainer.appendChild(visuallyImpairedIcon);
                div.appendChild(divContainer);

                return div;
            } catch (error) {
                console.warn('Ошибка создания элемента доступности для логина:', error);
                return document.createElement('div');
            }
        }
    };

    // Инициализируем плагин после загрузки DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                AccessibilityPlugin.init();
            }, 100);
        });
    } else {
        setTimeout(function() {
            AccessibilityPlugin.init();
        }, 100);
    }

})();

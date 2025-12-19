/**
 * Быстрая интеграция плагина доступности в тему Moodle
 * Поддерживает разные темы включая header-tools
 */
(function() {
    'use strict';
    
    // Список селекторов для поиска контейнера
    var selectors = [
        '#usernavigation',
        '.header-tools.type-text.tools-pos2',
        '.navbar-nav.ms-auto',
        '.usermenu-container',
        '.popover-region-notifications'
    ];
    
    // Ждем загрузки DOM
    function initAccessibility() {
        var container = null;
        var selector = null;
        
        // Пробуем найти подходящий контейнер
        for (var i = 0; i < selectors.length; i++) {
            container = document.querySelector(selectors[i]);
            if (container) {
                selector = selectors[i];
                console.log('Найден контейнер:', selector);
                break;
            }
        }
        
        if (!container) {
            console.warn('Не найден ни один из контейнеров:', selectors);
            addFloatingButton();
            return;
        }
        
        // Проверяем, не добавлена ли уже кнопка
        if (document.getElementById('quick-accessibility-btn')) {
            return;
        }
        
        // Создаем кнопку доступности
        var btn = document.createElement('div');
        btn.id = 'quick-accessibility-btn';
        btn.className = 'd-flex align-items-stretch';
        btn.style.cssText = 'margin-right: 10px;';
        btn.innerHTML = 
            '<div class="popover-region collapsed">' +
            '<a href="#" class="nav-link popover-region-toggle position-relative icon-no-margin bvi-open" ' +
            'role="button" title="Доступность для слабовидящих">' +
            '<i class="icon fa fa-eye fa-fw" title="Доступность"></i>' +
            '</a></div>';
        
        // Вставляем кнопку после найденного контейнера
        container.parentNode.insertBefore(btn, container.nextSibling);
        
        console.log('Кнопка доступности добавлена после:', selector);
    }
    
    // Добавляет плавающую кнопку как fallback
    function addFloatingButton() {
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
    }
    
    // Инициализация BVI плагина
    function initBVI() {
        var attempts = 0;
        var checkBVI = setInterval(function() {
            attempts++;
            
            if (typeof window.isvek !== 'undefined' && window.isvek.Bvi) {
                clearInterval(checkBVI);
                
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
                
                console.log('BVI плагин инициализирован');
            } else if (attempts >= 50) {
                clearInterval(checkBVI);
                console.warn('BVI плагин не загрузился');
            }
        }, 100);
    }
    
    // Запуск
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                initAccessibility();
                initBVI();
            }, 500);
        });
    } else {
        setTimeout(function() {
            initAccessibility();
            initBVI();
        }, 500);
    }
    
})();

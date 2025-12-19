/**
 * Интеграция плагина доступности для тем с header-tools
 * Добавляет кнопку после .header-tools.type-text.tools-pos2
 */
(function() {
    'use strict';
    
    function initAccessibility() {
        // Ищем контейнер header-tools
        var headerTools = document.querySelector('.header-tools.type-text.tools-pos2');
        if (!headerTools) {
            console.warn('Контейнер .header-tools.type-text.tools-pos2 не найден');
            return;
        }
        
        // Проверяем, не добавлена ли уже кнопка
        if (document.getElementById('header-tools-accessibility-btn')) {
            return;
        }
        
        // Создаем кнопку доступности
        var btn = document.createElement('div');
        btn.id = 'header-tools-accessibility-btn';
        btn.className = 'd-flex align-items-stretch';
        btn.style.cssText = 'margin-right: 10px;';
        btn.innerHTML = 
            '<div class="popover-region collapsed">' +
            '<a href="#" class="nav-link popover-region-toggle position-relative icon-no-margin bvi-open" ' +
            'role="button" title="Доступность для слабовидящих">' +
            '<i class="icon fa fa-eye fa-fw" title="Доступность"></i>' +
            '</a></div>';
        
        // Вставляем кнопку после header-tools
        headerTools.parentNode.insertBefore(btn, headerTools.nextSibling);
        
        console.log('Кнопка доступности добавлена после header-tools');
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
                
                console.log('BVI плагин инициализирован для header-tools темы');
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

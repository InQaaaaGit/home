# Коды кнопок доступности

## 🎯 Готовые коды кнопок для вставки

### 1. Простая кнопка (рекомендуется)
```html
<button class="bvi-open" style="
    background: #007bff;
    color: white;
    border: none;
    padding: 12px 16px;
    border-radius: 50px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
">
    <span style="font-size: 18px;">��️</span>
    <span>Доступность</span>
</button>
```

### 2. Плавающая кнопка (всегда видна)
```html
<button class="bvi-open" style="
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 999999;
    background: #007bff;
    color: white;
    border: none;
    padding: 12px 16px;
    border-radius: 50px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
">
    <span style="font-size: 18px;">👁️</span>
    <span>Доступность</span>
</button>
```

### 3. Кнопка-ссылка
```html
<a href="#" class="bvi-open" style="
    display: inline-block;
    background: #007bff;
    color: white;
    text-decoration: none;
    padding: 12px 16px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    transition: all 0.3s ease;
">
    👁️ Доступность
</a>
```

### 4. Минималистичная кнопка
```html
<button class="bvi-open" style="
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px;
">
    Доступность
</button>
```

### 5. Кнопка в стиле Moodle
```html
<button class="bvi-open btn btn-primary" style="
    padding: 8px 12px;
    font-size: 12px;
    border-radius: 3px;
">
    👁️ Доступность
</button>
```

### 6. Кнопка в левом верхнем углу
```html
<button class="bvi-open" style="
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 999999;
    background: #dc3545;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px;
">
    👁️ Доступность
</button>
```

### 7. Кнопка в нижнем правом углу
```html
<button class="bvi-open" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 999999;
    background: #17a2b8;
    color: white;
    border: none;
    padding: 12px 16px;
    border-radius: 50px;
    cursor: pointer;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(23,162,184,0.3);
">
    👁️ Доступность
</button>
```

### 8. Кнопка в навигации
```html
<li class="nav-item">
    <a class="nav-link bvi-open" href="#" style="
        color: #007bff;
        font-weight: bold;
    ">
        👁️ Доступность
    </a>
</li>
```

## 🔧 JavaScript код для инициализации

```javascript
<!-- Подключение CSS -->
<link rel="stylesheet" href="/local/cdo_visuallyimpaired/plugins/dist/css/bvi.min.css">

<!-- Подключение JavaScript -->
<script src="/local/cdo_visuallyimpaired/plugins/dist/js/bvi.min.js"></script>

<!-- Инициализация -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ждем загрузки BVI плагина
    var checkBVI = setInterval(function() {
        if (typeof window.isvek !== 'undefined' && window.isvek.Bvi) {
            clearInterval(checkBVI);
            
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
            console.log('BVI плагин инициализирован');
        }
    }, 100);
});
</script>
```

## 📋 Инструкция по использованию:

1. **Скопируйте HTML код** кнопки в нужное место на странице
2. **Подключите CSS:** `<link rel="stylesheet" href="/local/cdo_visuallyimpaired/plugins/dist/css/bvi.min.css">`
3. **Подключите JavaScript:** `<script src="/local/cdo_visuallyimpaired/plugins/dist/js/bvi.min.js"></script>`
4. **Добавьте инициализацию** BVI плагина
5. **Убедитесь, что класс** `bvi-open` присутствует на кнопке

## ⚠️ Важные моменты:

- **Класс `bvi-open` обязателен** для работы кнопки
- Можно использовать любой HTML элемент: `<button>`, `<a>`, `<div>`
- Стили можно изменить по своему усмотрению
- Плавающая кнопка (`position: fixed`) всегда будет видна
- `z-index: 999999` обеспечивает отображение поверх других элементов

## 🎨 Настройка цветов:

- **Синий:** `#007bff` (основной)
- **Зеленый:** `#28a745` (успех)
- **Красный:** `#dc3545` (опасность)
- **Голубой:** `#17a2b8` (информация)
- **Серый:** `#6c757d` (вторичный)

## 📱 Адаптивность:

Для мобильных устройств добавьте:
```css
@media (max-width: 768px) {
    .bvi-open {
        font-size: 12px !important;
        padding: 8px 12px !important;
    }
}
```

## 🔗 Правильные пути к файлам:

- **CSS:** `/local/cdo_visuallyimpaired/plugins/dist/css/bvi.min.css`
- **JS:** `/local/cdo_visuallyimpaired/plugins/dist/js/bvi.min.js`
- **Иконки:** `/local/cdo_visuallyimpaired/plugins/dist/img/`

## 📁 Структура файлов:

```
local/cdo_visuallyimpaired/
├── plugins/
│   └── dist/
│       ├── css/
│       │   └── bvi.min.css
│       ├── js/
│       │   └── bvi.min.js
│       └── img/
│           ├── adjust.svg
│           ├── cog.svg
│           ├── eye.svg
│           └── ...
├── js/
│   ├── init-simple.js
│   └── debug-simple.js
└── lib.php
```

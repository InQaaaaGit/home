# CDO Certification Sheet - Vue 3+ с Pinia

## Миграция с Vue 2 на Vue 3+

Проект полностью мигрирован с Vue 2 + Vuex на Vue 3 + Pinia с использованием Composition API.

### Структура проекта

```
vue/
├── components/
│   ├── version3/              # Новые компоненты Vue 3+
│   │   ├── sheets.vue         # Главный компонент
│   │   ├── students-table.vue # Таблица студентов
│   │   ├── sheet-buttons.vue  # Кнопки управления
│   │   ├── sheet-info.vue     # Информация о ведомости
│   │   ├── commission_table.vue # Таблица комиссии
│   │   └── not-found-open-sheet.vue # Сообщение об отсутствии ведомостей
│   └── [старые компоненты Vue 2]
├── stores/
│   └── certification.js       # Pinia store
├── main.js                    # Точка входа приложения
├── webpack.config.js          # Конфигурация сборки
└── package.json               # Зависимости
```

### Ключевые изменения

#### 1. Vue 3 Composition API
- Все компоненты переписаны с использованием `<script setup>`
- Использование `ref()`, `computed()`, `onMounted()` и других Composition API функций
- Улучшенная типизация и читаемость кода

#### 2. Pinia вместо Vuex
- Создан `useCertificationStore` с использованием `defineStore`
- Реактивное состояние с `ref()` и `computed()`
- Actions для асинхронных операций
- Улучшенная поддержка TypeScript

#### 3. Обновленные зависимости
- Vue 3.4+
- Pinia 2.1.7
- vue-toastification 2.0.0-rc.5
- Webpack 5
- Современные инструменты сборки

### Именование файлов

Итоговые AMD файлы генерируются с указанием режима сборки и обязательным префиксом `-lazy` (без точек):

- **Development**: `dev-app-lazy.min.js` (1.9 MB)
- **Production**: `prod-app-lazy.min.js` (135 KB)

### Доступные команды

```bash
# Сборка для разработки
npm run build:dev
npm run dev

# Сборка для продакшена
npm run build:prod
npm run build

# Режим наблюдения
npm run watch

# Горячая перезагрузка
npm run watch-hot
```

### Использование в Moodle

В PHP коде Moodle можно динамически выбирать нужную версию:

```php
$buildMode = $CFG->debugdeveloper ? 'dev' : 'prod';
$jsFile = "{$buildMode}-app-lazy.min.js";
```

### Преимущества миграции

1. **Производительность** - Vue 3 быстрее Vue 2
2. **Размер бандла** - Production версия в 14 раз меньше
3. **TypeScript поддержка** - Лучшая типизация
4. **Composition API** - Более гибкая организация кода
5. **Pinia** - Проще и мощнее Vuex
6. **Современные инструменты** - Webpack 5, современные загрузчики

### Структура Pinia Store

```javascript
export const useCertificationStore = defineStore('certification', () => {
  // State
  const strings = ref({});
  const sheets = ref([]);
  const userID = ref(null);
  
  // Getters
  const haveSheets = computed(() => sheets.value.length > 0);
  
  // Actions
  const loadListSheet = async () => { /* ... */ };
  const insertGrade = async (parameters) => { /* ... */ };
  
  return { /* ... */ };
});
```

### Миграция компонентов

Все компоненты мигрированы с Vue 2 Options API на Vue 3 Composition API:

**Было (Vue 2):**
```javascript
export default {
  props: ['sheet'],
  computed: {
    ...mapState(['strings', 'sheets']),
    ...mapGetters(['haveSheets'])
  },
  methods: {
    ...mapActions(['loadListSheet'])
  }
}
```

**Стало (Vue 3):**
```javascript
<script setup>
import { storeToRefs } from 'pinia';
import { useCertificationStore } from '../../stores/certification.js';

const props = defineProps({
  sheet: { type: Object, required: true }
});

const certificationStore = useCertificationStore();
const { strings, sheets, haveSheets } = storeToRefs(certificationStore);
</script>
```

### Совместимость

- ✅ Полная совместимость с Moodle 4.5+
- ✅ Поддержка PHP 8.1+
- ✅ AMD модули для Moodle
- ✅ Внешние зависимости (core/ajax, core/notification)
- ✅ Bootstrap стили
- ✅ Font Awesome иконки

### Отладка

Development версия включает:
- Source maps для отладки
- Подробные сообщения об ошибках
- Vue DevTools поддержка
- Горячая перезагрузка

Production версия:
- Минифицированный код
- Оптимизированный размер
- Удалены отладочные сообщения

## Решение проблемы CSS

### Проблема
При использовании vue-toastification возникает ошибка:
```
Cannot find module 'vue-toastification/dist/index.css'
```

### Решение
1. **Убран импорт CSS** из main.js
2. **CSS файл скопирован** в папку build: `vue-toastification.css`
3. **В PHP Moodle** добавить:
```php
$PAGE->requires->css('/local/cdo_certification_sheet/amd/build/vue-toastification.css');
```

### Файлы в build папке
```
amd/build/
├── dev-app-lazy.min.js        # Development версия (1.8 MB)
├── prod-app-lazy.min.js       # Production версия (113 KB)
├── vue-toastification.css     # CSS стили для уведомлений (16 KB)
└── prod-app-lazy.min.js.LICENSE.txt
```

### Преимущества решения
- ✅ Сборка без ошибок
- ✅ Меньший размер JS файлов
- ✅ CSS загружается через Moodle
- ✅ Полная совместимость с Moodle

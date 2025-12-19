import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import Main from "./components/Main.vue";
import { useAppStore } from './stores/app';
import router from './router';

/**
 * Инициализация Vue 3 приложения для системы оценки качества образования
 * @param {Object} config - Конфигурация приложения
 * @param {string} config.app_type - Тип приложения ('admin' или 'survey')
 * @param {Array} config.years - Список годов (для admin)
 * @param {Array} config.reports - Список отчетов (для admin)
 * @param {string} config.discipline - Название дисциплины (для survey)
 * @param {string} config.discipline_code - Код дисциплины (для survey)
 * @param {number} config.group_tab - Номер группы/вкладки (для survey)
 * @param {string} config.lang_strings - JSON строка с переводами
 * @param {string} config.initial_route - Начальный роут (опционально)
 */
function init({
                  app_type,
                  years,
                  reports,
                  discipline,
                  discipline_code,
                  group_tab,
                  lang_strings,
                  initial_route
              }) {

    const app = createApp(Main, {
        appType: app_type
    });
    const pinia = createPinia();

    app.use(pinia);
    app.use(router);

    // Инициализируем store с данными приложения
    const appStore = useAppStore();
    
    // Парсим строки локализации
    let langStrings = {};
    try {
        if (typeof lang_strings === 'string') {
            langStrings = JSON.parse(lang_strings);
        } else if (typeof lang_strings === 'object') {
            langStrings = lang_strings;
        }
    } catch (e) {
        console.error('Ошибка парсинга lang_strings:', e);
        langStrings = {};
    }
    
    console.log('Loaded lang strings:', langStrings);
    
    // Инициализация в зависимости от типа приложения
    if (app_type === 'admin') {
        appStore.initializeAdmin({
            langStrings: langStrings,
            years: years || [],
            reports: reports || []
        });
    } else if (app_type === 'survey') {
        appStore.initializeSurvey({
            langStrings: langStrings,
            discipline: discipline || '',
            disciplineCode: discipline_code || '',
            groupTab: group_tab || 0
        });
    }
    
    // Настройка Toast уведомлений
    app.use(Toast, {
        position: "top-right",
        timeout: 3000,
        closeOnClick: true,
        pauseOnFocusLoss: true,
        pauseOnHover: true,
        draggable: true,
        draggablePercent: 0.6,
        showCloseButtonOnHover: false,
        hideProgressBar: false,
        closeButton: "button",
        icon: true,
        rtl: false
    });

    // Монтируем приложение
    app.mount('#main_app');

    // Переходим на начальный роут, если указан
    if (initial_route) {
        router.push(initial_route);
    } else {
        // Определяем начальный роут по типу приложения
        if (app_type === 'admin') {
            router.push('/admin');
        } else if (app_type === 'survey') {
            router.push('/survey');
        }
    }
}

export { init };

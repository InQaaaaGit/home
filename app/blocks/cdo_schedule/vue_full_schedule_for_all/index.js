import App from './components/Main.vue';
import {createApp} from "vue";
import {createPinia} from 'pinia';

import {useLangStore} from "./store/storeLang";
import {useParamsStore} from "./store/storeParams";

import notification from "core/notification";

// Импорт глобальных стилей
import './styles/global.css';
// Импорт стилей для месячного вида
import './styles/scheduler-month.css';

async function init() {

        const pinia = createPinia();
        const app = createApp(App);

        app.use(pinia);

        // Инициализируем store с параметрами
        const paramsStore = useParamsStore();
        paramsStore.initializeParams();

        const lang = useLangStore();
        await lang.loadComponentStrings('block_cdo_schedule');

        // Монтируем приложение
        app.mount('#main_app');

}

export {
    init
};

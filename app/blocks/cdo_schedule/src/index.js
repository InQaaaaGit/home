import {createApp} from "vue";
import {createPinia} from 'pinia';
import App from './App.vue';
import {useParamsStore} from "./store/storeParams";
import {useLangStore} from "./store/storeLang";
import notification from "core/notification";

// Импорт глобальных стилей
import './styles/global.css';
// Импорт стилей для месячного вида
import './styles/scheduler-month.css';

async function init() {
    try {
        const pinia = createPinia();
        const app = createApp(App);

        app.config.errorHandler = function(err, vm, info) {
            console.error(err, info);
            notification.exception(err);
        };

        app.use(pinia);

        const paramsStore = useParamsStore();
        paramsStore.initializeParams();

        const lang = useLangStore();
        await lang.loadComponentStrings('block_cdo_schedule');

        app.mount('#main_app');
    } catch (e) {
        notification.exception(e);
    }
}

export {
    init
};

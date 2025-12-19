import App from './components/attendance.vue';
import {createApp} from "vue";
import {createPinia} from 'pinia';
import {useMainStore} from './store/store';
import {useLangStore} from "./store/storeLang";

async function init(params) {
    const pinia = createPinia();
    const app = createApp(App);
    app.use(pinia);
    const store = useMainStore();
    const lang = useLangStore();
    lang.loadComponentStrings('block_cdo_schedule');
    await store.setParams(params);
    await store.getSetAttendance();
    app.mount('#main_app');
}

export {
    init
};

import Main from './components/availability.vue';
import {createPinia} from 'pinia';
import {createApp, onErrorCaptured} from "vue";
import { mask } from 'vue-the-mask';
import ToastPlugin from 'vue-toast-notification';
import notification from "core/notification";
import {useLangStore} from "./store/storeLang";
import {useMainStore} from "./store/store";

/*
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
*/

async function init() {
    const pinia = createPinia();
    const app = createApp(Main);
    app.use(ToastPlugin);
    app.use(pinia);
  /*  const vuetify = createVuetify({
        components,
        directives,
    })
    app.use(vuetify);*/
    const mainStore = useMainStore();
    const langStore = useLangStore();

    app.directive('mask', mask);
    app.config.errorHandler = (err, instance, info) => {
        console.log("Global error:", err);
        console.log("Vue instance:", instance);
        console.log("Error info:", info);
        notification.alert(
            'Ошибка',
            info,
            'Отмена'
        );
    };

    app.mount('#main_app');
  //  await mainStore.loadUsers();
    await langStore.loadComponentStrings('local_cdo_ag_tools');
}

export {
    init
};

